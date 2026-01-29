<?php namespace Admin\Services\Session\Drivers;

use Admin\Services\Session\Interfaces\DriverInterface;
use Admin\Services\Session\Driver;
use Redis as NativeRedis;
use RedisException;

/**
 * Redis Session Driver
 */
class Redis extends Driver implements DriverInterface
{
    protected $redis;
    protected $keyPrefix = 'mta_cms_admin_session:';
    protected $lockKey;
    protected $keyExists = false;
    protected $setTimeoutName;
    protected $deleteName;
    protected $pingSuccess;

    /**
     * Constructor
     *
     * @param array $params
     */
    public function __construct(array &$params)
    {
        parent::__construct($params);

        if (version_compare((string)phpversion('redis'), '5', '>=')) {
            $this->setTimeoutName = 'expire';
            $this->deleteName = 'del';
            $this->pingSuccess = true;
        } else {
            $this->setTimeoutName = 'setTimeout';
            $this->deleteName = 'delete';
            $this->pingSuccess = '+PONG';
        }

        if (empty($this->config['save_path'])) {
            \Admin\Core\Error::logMessage('error', 'Session: No Redis save path configured.');
        } elseif (preg_match('#(?:tcp://)?([^:?]+)(?:\:(\d+))?(\?.+)?#', (string)$this->config['save_path'], $matches)) {
            isset($matches[3]) or $matches[3] = '';
            $this->config['save_path'] = [
                'host' => $matches[1],
                'port' => empty($matches[2]) ? null : $matches[2],
                'password' => preg_match('#auth=([^\s&]+)#', $matches[3], $match) ? $match[1] : null,
                'database' => preg_match('#database=(\d+)#', $matches[3], $match) ? (int)$match[1] : null,
                'timeout' => preg_match('#timeout=(\d+\.\d+)#', $matches[3], $match) ? (float)$match[1] : null
            ];
            preg_match('#prefix=([^\s&]+)#', $matches[3], $match) && $this->keyPrefix = $match[1];
        } else {
            \Admin\Core\Error::logMessage('error', 'Session: Invalid Redis save path format: ' . $this->config['save_path']);
        }

        if ($this->config['match_ip'] === true) {
            $this->keyPrefix .= (string)$_SERVER['REMOTE_ADDR'] . ':';
        }
    }

    /**
     * Open session
     */
    public function open(string $savePath, string $name): bool
    {
        if (empty($this->config['save_path'])) {
            return $this->failure;
        }

        try {
            $redis = new NativeRedis();
            if (!$redis->connect((string)$this->config['save_path']['host'], (int)$this->config['save_path']['port'], (float)$this->config['save_path']['timeout'])) {
                \Admin\Core\Error::logMessage('error', 'Session: Unable to connect to Redis with the configured settings.');
            } elseif (isset($this->config['save_path']['password']) && !$redis->auth((string)$this->config['save_path']['password'])) {
                \Admin\Core\Error::logMessage('error', 'Session: Unable to authenticate to Redis instance.');
            } elseif (isset($this->config['save_path']['database']) && !$redis->select((int)$this->config['save_path']['database'])) {
                \Admin\Core\Error::logMessage('error', 'Session: Unable to select Redis database with index ' . $this->config['save_path']['database']);
            } else {
                $this->redis = $redis;
                return $this->success;
            }
        } catch (RedisException $e) {
            \Admin\Core\Error::logMessage('error', 'Session: Redis connection error: ' . $e->getMessage());
        }

        return $this->failure;
    }

    /**
     * Read session data
     */
    public function read(string $sessionId): string|false
    {
        if (isset($this->redis) && $this->getLock($sessionId)) {
            $this->sessionId = $sessionId;
            $sessionData = $this->redis->get((string)$this->keyPrefix . $sessionId);
            if (is_string($sessionData)) {
                $this->keyExists = true;
            } else {
                $this->keyExists = false;
                $sessionData = '';
            }
            $this->fingerprint = md5($sessionData);
            return $sessionData;
        }
        return false;
    }

    /**
     * Write session data
     */
    public function write(string $sessionId, string $sessionData): bool
    {
        if (!isset($this->redis, $this->lockKey)) {
            return $this->failure;
        } elseif ($sessionId !== $this->sessionId) {
            if (!$this->releaseLock() || !$this->getLock($sessionId)) {
                return $this->failure;
            }
            $this->keyExists = false;
            $this->sessionId = $sessionId;
        }

        $this->redis->{$this->setTimeoutName}((string)$this->lockKey, 300);
        $fingerprint = md5((string)$sessionData);
        if ($this->fingerprint !== $fingerprint || $this->keyExists === false) {
            if ($this->redis->set((string)$this->keyPrefix . $sessionId, $sessionData, (int)$this->config['expiration'])) {
                $this->fingerprint = $fingerprint;
                $this->keyExists = true;
                return $this->success;
            }
            return $this->failure;
        }

        return (bool)$this->redis->{$this->setTimeoutName}((string)$this->keyPrefix . $sessionId, (int)$this->config['expiration']);
    }

    /**
     * Close session
     */
    public function close(): bool
    {
        if (isset($this->redis)) {
            try {
                if ($this->redis->ping() === $this->pingSuccess) {
                    $this->releaseLock();
                    if ($this->redis->close() === false) {
                        return $this->failure;
                    }
                }
            } catch (RedisException $e) {
                \Admin\Core\Error::logMessage('error', 'Session: Got RedisException on close(): ' . $e->getMessage());
            }
            $this->redis = null;
            return $this->success;
        }

        return $this->success;
    }

    /**
     * Destroy session
     */
    public function destroy(string $sessionId): bool
    {
        if (isset($this->redis, $this->lockKey)) {
            $this->redis->{$this->deleteName}((string)$this->keyPrefix . $sessionId);
            $this->cookieDestroy();
            return $this->success;
        }

        return $this->failure;
    }

    /**
     * Garbage Collection
     */
    public function gc(int $maxlifetime): int|false
    {
        return 0;
    }

    /**
     * Update Timestamp
     */
    public function updateTimestamp(string $sessionId, string $data): bool
    {
        return (bool)$this->redis->{$this->setTimeoutName}((string)$this->keyPrefix . $sessionId, (int)$this->config['expiration']);
    }

    /**
     * Validate ID
     */
    public function validateId(string $sessionId): bool
    {
        return (bool)$this->redis->exists((string)$this->keyPrefix . $sessionId);
    }

    /**
     * Get lock
     */
    protected function getLock($sessionId)
    {
        if ($this->lockKey === (string)$this->keyPrefix . $sessionId . ':lock') {
            return $this->redis->{$this->setTimeoutName}((string)$this->lockKey, 300);
        }

        $lockKey = (string)$this->keyPrefix . $sessionId . ':lock';
        $attempt = 0;
        do {
            $ttl = (int)$this->redis->ttl($lockKey);
            if ($ttl > 0) {
                sleep(1);
                continue;
            }

            if ($ttl === -2 && !$this->redis->set($lockKey, (string)time(), ['nx', 'ex' => 300])) {
                sleep(1);
                continue;
            } elseif (!$this->redis->setex($lockKey, 300, (string)time())) {
                \Admin\Core\Error::logMessage('error', 'Session: Error while trying to obtain lock for ' . $this->keyPrefix . $sessionId);
                return false;
            }

            $this->lockKey = $lockKey;
            break;
        } while (++$attempt < 30);

        if ($attempt === 30) {
            \Admin\Core\Error::logMessage('error', 'Session: Unable to obtain lock for ' . $this->keyPrefix . $sessionId . ' after 30 attempts, aborting.');
            return false;
        }

        $this->lock = true;
        return true;
    }

    /**
     * Release lock
     */
    protected function releaseLock()
    {
        if (isset($this->redis, $this->lockKey) && $this->lock) {
            if (!$this->redis->{$this->deleteName}((string)$this->lockKey)) {
                \Admin\Core\Error::logMessage('error', 'Session: Error while trying to free lock for ' . $this->lockKey);
                return false;
            }
            $this->lockKey = null;
            $this->lock = false;
        }

        return true;
    }
}
