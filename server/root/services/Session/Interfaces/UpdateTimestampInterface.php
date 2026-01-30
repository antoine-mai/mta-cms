<?php namespace Root\Services\Session\Interfaces;

/**
 * Update Timestamp Interface
 */
interface UpdateTimestampInterface
{
    public function updateTimestamp(string $sessionId, string $data): bool;
    public function validateId(string $sessionId): bool;
}
