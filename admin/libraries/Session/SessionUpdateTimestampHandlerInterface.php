<?php
defined('ADMIN_ROOT') OR exit('No direct script access allowed');
interface SessionUpdateTimestampHandlerInterface {
	public function updateTimestamp($session_id, $data);
	public function validateId($session_id);
}
