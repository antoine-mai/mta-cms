<?php namespace Admin\Services\Cache\Drivers;

use Admin\Services\Driver;

class Dummy extends Driver {
	public function get($id)
	{
		return false;
	}

	public function save($id, $data, $ttl = 60, $raw = false)
	{
		return true;
	}

	public function delete($id)
	{
		return true;
	}

	public function increment($id, $offset = 1)
	{
		return true;
	}

	public function decrement($id, $offset = 1)
	{
		return true;
	}

	public function clean()
	{
		return true;
	}

	public function cache_info($type = null)
	{
		return false;
	}

	public function get_metadata($id)
	{
		return false;
	}

	public function is_supported()
	{
		return true;
	}
}
