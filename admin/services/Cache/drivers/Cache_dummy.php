<?php
defined('ADMIN_ROOT') OR exit('No direct script access allowed');
class Cache_dummy extends Driver {
	public function get($id)
	{
		return FALSE;
	}
	public function save($id, $data, $ttl = 60, $raw = FALSE)
	{
		return TRUE;
	}
	public function delete($id)
	{
		return TRUE;
	}
	public function increment($id, $offset = 1)
	{
		return TRUE;
	}
	public function decrement($id, $offset = 1)
	{
		return TRUE;
	}
	public function clean()
	{
		return TRUE;
	}
	public function cache_info($type = NULL)
	{
		return FALSE;
	}
	public function get_metadata($id)
	{
		return FALSE;
	}
	public function is_supported()
	{
		return TRUE;
	}
}
