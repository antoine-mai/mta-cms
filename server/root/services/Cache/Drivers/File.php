<?php namespace Root\Services\Cache\Drivers;

use Root\Services\Driver;

class File extends Driver {
	protected $_cache_path;

	public function __construct()
	{
		$mta =& \Root\Core\Controller::getInstance();
		$mta->load->helper('file');
		$path = $mta->config->item('cache_path');
		$this->_cache_path = ($path === '') ? $mta->config->getRoot().'cache/' : $path;
	}

	public function get($id)
	{
		$data = $this->_get($id);
		return is_array($data) ? $data['data'] : false;
	}

	public function save($id, $data, $ttl = 60, $raw = false)
	{
		$contents = [
			'time'		=> time(),
			'ttl'		=> $ttl,
			'data'		=> $data
		];
		if (write_file((string)$this->_cache_path.$id, serialize($contents)))
		{
			chmod((string)$this->_cache_path.$id, 0640);
			return true;
		}
		return false;
	}

	public function delete($id)
	{
		return is_file((string)$this->_cache_path.$id) ? unlink((string)$this->_cache_path.$id) : false;
	}

	public function increment($id, $offset = 1)
	{
		$data = $this->_get($id);
		if ($data === false)
		{
			$data = ['data' => 0, 'ttl' => 60];
		}
		elseif ( ! is_int($data['data']))
		{
			return false;
		}
		$new_value = $data['data'] + $offset;
		return $this->save($id, $new_value, $data['ttl'])
			? $new_value
			: false;
	}

	public function decrement($id, $offset = 1)
	{
		$data = $this->_get($id);
		if ($data === false)
		{
			$data = ['data' => 0, 'ttl' => 60];
		}
		elseif ( ! is_int($data['data']))
		{
			return false;
		}
		$new_value = $data['data'] - $offset;
		return $this->save($id, $new_value, $data['ttl'])
			? $new_value
			: false;
	}

	public function clean()
	{
		return delete_files((string)$this->_cache_path, false, true);
	}

	public function cache_info($type = null)
	{
		return get_dir_file_info((string)$this->_cache_path);
	}

	public function get_metadata($id)
	{
		if ( ! is_file((string)$this->_cache_path.$id))
		{
			return false;
		}
		$data = unserialize((string)file_get_contents((string)$this->_cache_path.$id));
		if (is_array($data))
		{
			$mtime = filemtime((string)$this->_cache_path.$id);
			if ( ! isset($data['ttl'], $data['time']))
			{
				return false;
			}
			return [
				'expire' => $data['time'] + $data['ttl'],
				'mtime'	 => $mtime
			];
		}
		return false;
	}

	public function is_supported()
	{
		return is_really_writable((string)$this->_cache_path);
	}

	protected function _get($id)
	{
		if ( ! is_file((string)$this->_cache_path.$id))
		{
			return false;
		}
		$data = unserialize((string)file_get_contents((string)$this->_cache_path.$id));
		if ($data['ttl'] > 0 && time() > $data['time'] + $data['ttl'])
		{
			file_exists((string)$this->_cache_path.$id) && unlink((string)$this->_cache_path.$id);
			return false;
		}
		return $data;
	}
}
