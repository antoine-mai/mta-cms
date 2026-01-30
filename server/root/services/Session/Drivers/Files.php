<?php namespace Root\Services\Session\Drivers;
/**
 * Files Session Driver
**/
use \Root\Services\Session\Interfaces\DriverInterface;
use \Root\Services\Session\Driver;
use \Root\Core\Error;
/**
 * 
**/
class Files extends Driver implements DriverInterface
{
    protected $savePath;
    protected $fileHandle;
    protected $filePath;
    protected $fileNew;
    protected $sidRegexp;

    /**
     * Constructor
     *
     * @param array $params
     */
    public function __construct(array &$params)
    {
        parent::__construct($params);

        if (isset($this->config['save_path'])) {
            $this->config['save_path'] = rtrim((string)$this->config['save_path'], '/\\');
            ini_set('session.save_path', (string)$this->config['save_path']);
        } else {
            Error::error_log( 'Session: "sess_save_path" is empty; using "session.save_path" value from php.ini.');
            $this->config['save_path'] = rtrim((string)ini_get('session.save_path'), '/\\');
        }

        $this->sidRegexp = $this->config['_sid_regexp'];
    }

    /**
     * Open session
     */
    public function open(string $savePath, string $name): bool
    {
        if (!is_dir((string)$savePath)) {
            if (!mkdir((string)$savePath, 0700, true)) {
                Error::error_log( "Session: Configured save path '" . $this->config['save_path'] . "' is not a directory, doesn't exist or cannot be created.");
                return $this->failure;
            }
        } elseif (!is_writable((string)$savePath)) {
            Error::error_log( "Session: Configured save path '" . $this->config['save_path'] . "' is not writable by the PHP process.");
            return $this->failure;
        }

        $this->config['save_path'] = $savePath;
        $this->filePath = $this->config['save_path'] . DIRECTORY_SEPARATOR
            . (string)$name
            . ($this->config['match_ip'] ? md5((string)$_SERVER['REMOTE_ADDR']) : '');

        return $this->success;
    }

    /**
     * Read session data
     */
    public function read(string $sessionId): string|false
    {
        if ($this->fileHandle === null) {
            $this->fileNew = !file_exists((string)$this->filePath . $sessionId);
            if (($this->fileHandle = fopen((string)$this->filePath . $sessionId, 'c+b')) === false) {
                Error::error_log( "Session: Unable to open file '" . $this->filePath . $sessionId . "'.");
                return $this->failure;
            }

            if (flock($this->fileHandle, LOCK_EX) === false) {
                Error::error_log( "Session: Unable to obtain lock for file '" . $this->filePath . $sessionId . "'.");
                fclose($this->fileHandle);
                $this->fileHandle = null;
                return $this->failure;
            }

            $this->sessionId = $sessionId;

            if ($this->fileNew) {
                chmod((string)$this->filePath . $sessionId, 0600);
                $this->fingerprint = md5('');
                return '';
            }

            clearstatcache(true, (string)$this->filePath . $sessionId);
        } elseif ($this->fileHandle === false) {
            return $this->failure;
        } else {
            rewind($this->fileHandle);
        }

        $sessionData = '';
        $fileSize = (int)filesize((string)$this->filePath . $sessionId);
        for ($read = 0; $read < $fileSize; $read += strlen($buffer)) {
            if (($buffer = fread($this->fileHandle, $fileSize - $read)) === false) {
                break;
            }
            $sessionData .= $buffer;
        }

        $this->fingerprint = md5($sessionData);
        return $sessionData;
    }

    /**
     * Write session data
     */
    public function write(string $sessionId, string $sessionData): bool
    {
        if ($sessionId !== $this->sessionId && ($this->close() === $this->failure || $this->read($sessionId) === $this->failure)) {
            return $this->failure;
        }

        if (!is_resource($this->fileHandle)) {
            return $this->failure;
        } elseif ($this->fingerprint === md5((string)$sessionData)) {
            return (!$this->fileNew && !touch((string)$this->filePath . $sessionId))
                ? $this->failure
                : $this->success;
        }

        if (!$this->fileNew) {
            ftruncate($this->fileHandle, 0);
            rewind($this->fileHandle);
        }

        if (($length = strlen((string)$sessionData)) > 0) {
            $result = 0;
            for ($written = 0; $written < $length; $written += $result) {
                if (($result = fwrite($this->fileHandle, substr((string)$sessionData, (int)$written))) === false) {
                    break;
                }
            }

            if (!is_int($result)) {
                $this->fingerprint = md5(substr((string)$sessionData, 0, (int)$written));
                Error::error_log( 'Session: Unable to write data.');
                return $this->failure;
            }
        }

        $this->fingerprint = md5((string)$sessionData);
        return $this->success;
    }

    /**
     * Close session
     */
    public function close(): bool
    {
        if (is_resource($this->fileHandle)) {
            flock($this->fileHandle, LOCK_UN);
            fclose($this->fileHandle);
            $this->fileHandle = $this->fileNew = $this->sessionId = null;
        }

        return $this->success;
    }

    /**
     * Destroy session
     */
    public function destroy(string $sessionId): bool
    {
        if ($this->close() === $this->success) {
            if (file_exists((string)$this->filePath . $sessionId)) {
                $this->cookieDestroy();
                return unlink((string)$this->filePath . $sessionId)
                    ? $this->success
                    : $this->failure;
            }

            return $this->success;
        } elseif ($this->filePath !== null) {
            clearstatcache();
            if (file_exists((string)$this->filePath . $sessionId)) {
                $this->cookieDestroy();
                return unlink((string)$this->filePath . $sessionId)
                    ? $this->success
                    : $this->failure;
            }

            return $this->success;
        }

        return $this->failure;
    }

    /**
     * Garbage Collection
     */
    public function gc(int $maxlifetime): int|false
    {
        if (!is_dir((string)$this->config['save_path']) || ($directory = opendir((string)$this->config['save_path'])) === false) {
            Error::error_log( "Session: Garbage collector couldn't list files under directory '" . $this->config['save_path'] . "'.");
            return 0;
        }

        $ts = time() - (int)$maxlifetime;
        $pattern = ($this->config['match_ip'] === true) ? '[0-9a-f]{32}' : '';
        $pattern = sprintf('#\A%s' . $pattern . $this->sidRegexp . '\z#', preg_quote((string)$this->config['cookie_name']));

        $count = 0;
        while (($file = readdir($directory)) !== false) {
            if (
                !preg_match($pattern, (string)$file)
                || !is_file($this->config['save_path'] . DIRECTORY_SEPARATOR . $file)
                || ($mtime = filemtime((string)$this->config['save_path'] . DIRECTORY_SEPARATOR . $file)) === false
                || $mtime > $ts
            ) {
                continue;
            }

            unlink((string)$this->config['save_path'] . DIRECTORY_SEPARATOR . $file);
            $count++;
        }

        closedir($directory);
        return $count;
    }

    /**
     * Update Timestamp
     */
    public function updateTimestamp(string $sessionId, string $data): bool
    {
        return touch((string)$this->filePath . $sessionId);
    }

    /**
     * Validate ID
     */
    public function validateId(string $sessionId): bool
    {
        $result = is_file((string)$this->filePath . $sessionId);
        clearstatcache(true, (string)$this->filePath . $sessionId);
        return $result;
    }
}
