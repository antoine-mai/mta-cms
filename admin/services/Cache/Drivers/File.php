<?php namespace Admin\Services\Cache\Drivers;

use Admin\Services\Driver;

class File extends Driver {
	protected $_cache_path;

	public function __construct()
	{
		$CI =& get_instance();
		$CI->load->helper('file');
		$path = $CI->config->item('cache_path');
		$this->_cache_path = ($path === '') ? ADMIN_ROOT.'cache/' : $path;
	}

	public function get($id)
	{
		$data = $this->_get($id);
		return is_array($data) ? $data['data'] : FALSE;
	}

	public function save($id, $data, $ttl = 60, $raw = FALSE)
	{
		$contents = [
			'time'		=> time(),
			'ttl'		=> $ttl,
			'data'		=> $data
		];
		if (write_file((string)$this->_cache_path.$id, serialize($contents)))
		{
			chmod((string)$this->_cache_path.$id, 0640);
			return TRUE;
		}
		return FALSE;
	}

	public function delete($id)
	{
		return is_file((string)$this->_cache_path.$id) ? unlink((string)$this->_cache_path.$id) : FALSE;
	}

	public function increment($id, $offset = 1)
	{
		$data = $this->_get($id);
		if ($data === FALSE)
		{
			$data = ['data' => 0, 'ttl' => 60];
		}
		elseif ( ! is_int($data['data']))
		{
			return FALSE;
		}
		$new_value = $data['data'] + $offset;
		return $this->save($id, $new_value, $data['ttl'])
			? $new_value
			: FALSE;
	}

	public function decrement($id, $offset = 1)
	{
		$data = $this->_get($id);
		if ($data === FALSE)
		{
			$data = ['data' => 0, 'ttl' => 60];
		}
		elseif ( ! is_int($data['data']))
		{
			return FALSE;
		}
		$new_value = $data['data'] - $offset;
		return $this->save($id, $new_value, $data['ttl'])
			? $new_value
			: FALSE;
	}

	public function clean()
	{
		return delete_files((string)$this->_cache_path, FALSE, TRUE);
	}

	public function cache_info($type = NULL)
	{
		return get_dir_file_info((string)$this->_cache_path);
	}

	public function get_metadata($id)
	{
		if ( ! is_file((string)$this->_cache_path.$id))
		{
			return FALSE;
		}
		$data = unserialize((string)file_get_contents((string)$this->_cache_path.$id));
		if (is_array($data))
		{
			$mtime = filemtime((string)$this->_cache_path.$id);
			if ( ! isset($data['ttl'], $data['time']))
			{
				return FALSE;
			}
			return [
				'expire' => $data['time'] + $data['ttl'],
				'mtime'	 => $mtime
			];
		}
		return FALSE;
	}

	public function is_supported()
	{
		return is_really_writable((string)$this->_cache_path);
	}

	protected function _get($id)
	{
		if ( ! is_file((string)$this->_cache_path.$id))
		{
			return FALSE;
		}
		$data = unserialize((string)file_get_contents((string)$this->_cache_path.$id));
		if ($data['ttl'] > 0 && time() > $data['time'] + $data['ttl'])
		{
			file_exists((string)$this->_cache_path.$id) && unlink((string)$this->_cache_path.$id);
			return FALSE;
		}
		return $data;
	}
}
