<?php namespace Admin\Services\Session\Interfaces;

/**
 * Handler Interface
 */
interface HandlerInterface
{
    public function open(string $savePath, string $name): bool;
    public function close(): bool;
    public function read(string $sessionId): string|false;
    public function write(string $sessionId, string $sessionData): bool;
    public function destroy(string $sessionId): bool;
    public function gc(int $maxlifetime): int|false;
}
