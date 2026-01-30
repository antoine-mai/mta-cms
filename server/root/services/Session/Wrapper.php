<?php namespace Root\Services\Session;

use Root\Services\Session\Interfaces\DriverInterface;
use Root\Services\Session\Interfaces\HandlerInterface;
use Root\Services\Session\Interfaces\UpdateTimestampInterface;

/**
 * Session Wrapper
 *
 * A wrapper class to provide standard session handling.
 */
class Wrapper implements \SessionHandlerInterface, \SessionUpdateTimestampHandlerInterface
{
    /**
     * Session driver instance
     *
     * @var DriverInterface
     */
    protected DriverInterface $driver;

    /**
     * Constructor
     *
     * @param DriverInterface $driver
     */
    public function __construct(DriverInterface $driver)
    {
        $this->driver = $driver;
    }

    /**
     * Open session
     */
    public function open(string $savePath, string $name): bool
    {
        return $this->driver->open($savePath, $name);
    }

    /**
     * Close session
     */
    public function close(): bool
    {
        return $this->driver->close();
    }

    /**
     * Read session data
     */
    public function read(string $sessionId): string|false
    {
        return $this->driver->read($sessionId);
    }

    /**
     * Write session data
     */
    public function write(string $sessionId, string $data): bool
    {
        return $this->driver->write($sessionId, $data);
    }

    /**
     * Destroy session
     */
    public function destroy(string $sessionId): bool
    {
        return $this->driver->destroy($sessionId);
    }

    /**
     * Garbage Collection
     */
    public function gc(int $maxlifetime): int|false
    {
        return $this->driver->gc($maxlifetime);
    }

    /**
     * Update Timestamp
     */
    public function updateTimestamp(string $sessionId, string $data): bool
    {
        return $this->driver->updateTimestamp($sessionId, $data);
    }

    /**
     * Validate ID
     */
    public function validateId(string $sessionId): bool
    {
        return $this->driver->validateId($sessionId);
    }
}
