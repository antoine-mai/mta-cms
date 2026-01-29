<?php namespace Admin\Services\Session\Drivers;

use Admin\Services\Session\Interfaces\DriverInterface;
use Admin\Services\Session\Driver;
use Memcached as NativeMemcached;

/**
 * Memcached Session Driver
 */
class Memcached extends Driver implements DriverInterface
{
    protected $memcached;
    protected $keyPrefix = 'mta_cms_admin_session:';
    protected $lockKey;

    /**
     * Constructor
     *
     * @param array $params
     */
    public function __construct(array &$params)
    {
        parent::__construct($params);

        if (empty($this->config['save_path'])) {
            \Admin\Core\Error::logMessage('error', 'Session: No Memcached save path configured.');
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
        $this->memcached = new NativeMemcached();
        $this->memcached->setOption(NativeMemcached::OPT_BINARY_PROTOCOL, true);
        
        $serverList = [];
        foreach ($this->memcached->getServerList() as $server) {
            $serverList[] = $server['host'] . ':' . $server['port'];
        }

        if (!preg_match_all('#,?([^,:]+)\:(\d{1,5})(?:\:(\d+))?#', (string)$this->config['save_path'], $matches, PREG_SET_ORDER)) {
            $this->memcached = null;
            \Admin\Core\Error::logMessage('error', 'Session: Invalid Memcached save path format: ' . $this->config['save_path']);
            return $this->failure;
        }

        foreach ($matches as $match) {
            if (in_array($match[1] . ':' . $match[2], $serverList, true)) {
                \Admin\Core\Error::logMessage('debug', 'Session: Memcached server pool already has ' . $match[1] . ':' . $match[2]);
                continue;
            }

            if (!$this->memcached->addServer($match[1], (int)$match[2], isset($match[3]) ? (int)$match[3] : 0)) {
                \Admin\Core\Error::logMessage('error', 'Could not add ' . $match[1] . ':' . $match[2] . ' to Memcached server pool.');
            } else {
                $serverList[] = $match[1] . ':' . $match[2];
            }
        }

        if (empty($serverList)) {
            \Admin\Core\Error::logMessage('error', 'Session: Memcached server pool is empty.');
            return $this->failure;
        }

        return $this->success;
    }

    /**
     * Read session data
     */
    public function read(string $sessionId): string|false
    {
        if (isset($this->memcached) && $this->getLock($sessionId)) {
            $this->sessionId = $sessionId;
            $sessionData = (string)$this->memcached->get((string)$this->keyPrefix . $sessionId);
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
        if (!isset($this->memcached, $this->lockKey)) {
            return $this->failure;
        } elseif ($sessionId !== $this->sessionId) {
            if (!$this->releaseLock() || !$this->getLock($sessionId)) {
                return $this->failure;
            }
            $this->fingerprint = md5('');
            $this->sessionId = $sessionId;
        }

        $key = (string)$this->keyPrefix . $sessionId;
        $this->memcached->replace((string)$this->lockKey, time(), 300);

        if ($this->fingerprint !== ($fingerprint = md5((string)$sessionData))) {
            if ($this->memcached->set($key, $sessionData, (int)$this->config['expiration'])) {
                $this->fingerprint = $fingerprint;
                return $this->success;
            }
            return $this->failure;
        } elseif (
            $this->memcached->touch($key, (int)$this->config['expiration'])
            || ($this->memcached->getResultCode() === NativeMemcached::RES_NOTFOUND && $this->memcached->set($key, $sessionData, (int)$this->config['expiration']))
        ) {
            return $this->success;
        }

        return $this->failure;
    }

    /**
     * Close session
     */
    public function close(): bool
    {
        if (isset($this->memcached)) {
            $this->releaseLock();
            if (!$this->memcached->quit()) {
                return $this->failure;
            }
            $this->memcached = null;
            return $this->success;
        }

        return $this->success;
    }

    /**
     * Destroy session
     */
    public function destroy(string $sessionId): bool
    {
        if (isset($this->memcached, $this->lockKey)) {
            $this->memcached->delete((string)$this->keyPrefix . $sessionId);
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
        return $this->memcached->touch((string)$this->keyPrefix . $sessionId, (int)$this->config['expiration']);
    }

    /**
     * Validate ID
     */
    public function validateId(string $sessionId): bool
    {
        $this->memcached->get((string)$this->keyPrefix . $sessionId);
        return ($this->memcached->getResultCode() === NativeMemcached::RES_SUCCESS);
    }

    /**
     * Get lock
     */
    protected function getLock($sessionId)
    {
        if ($this->lockKey === (string)$this->keyPrefix . $sessionId . ':lock') {
            if (!$this->memcached->replace((string)$this->lockKey, time(), 300)) {
                return ($this->memcached->getResultCode() === NativeMemcached::RES_NOTFOUND)
                    ? $this->memcached->add((string)$this->lockKey, time(), 300)
                    : false;
            }
            return true;
        }

        $lockKey = (string)$this->keyPrefix . $sessionId . ':lock';
        $attempt = 0;
        do {
            if ($this->memcached->get($lockKey)) {
                sleep(1);
                continue;
            }

            $method = ($this->memcached->getResultCode() === NativeMemcached::RES_NOTFOUND) ? 'add' : 'set';
            if (!$this->memcached->$method($lockKey, time(), 300)) {
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
        if (isset($this->memcached, $this->lockKey) && $this->lock) {
            if (!$this->memcached->delete((string)$this->lockKey) && $this->memcached->getResultCode() !== NativeMemcached::RES_NOTFOUND) {
                \Admin\Core\Error::logMessage('error', 'Session: Error while trying to free lock for ' . $this->lockKey);
                return false;
            }
            $this->lockKey = null;
            $this->lock = false;
        }

        return true;
    }
}
