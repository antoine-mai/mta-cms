<?php namespace Admin\Services\Session;

interface SessionUpdateTimestampHandlerInterface {
	public function updateTimestamp($session_id, $data);
	public function validateId($session_id);
}
