<?php namespace Root\Services\Session\Interfaces;

/**
 * Driver Interface
 */
interface DriverInterface
{
    public function open(string $savePath, string $name): bool;
    public function close(): bool;
    public function read(string $sessionId): string|false;
    public function write(string $sessionId, string $sessionData): bool;
    public function destroy(string $sessionId): bool;
    public function gc(int $maxlifetime): int|false;
    public function updateTimestamp(string $sessionId, string $data): bool;
    public function validateId(string $sessionId): bool;
}
