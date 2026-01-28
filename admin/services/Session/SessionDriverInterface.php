<?php namespace Admin\Services\Session;
/**
 * 
**/
interface SessionDriverInterface
{
	public function open($save_path, $name);
	public function close();
	public function read($session_id);
	public function write($session_id, $session_data);
	public function destroy($session_id);
	public function gc($maxlifetime);
	public function updateTimestamp($session_id, $data);
	public function validateId($session_id);
}
