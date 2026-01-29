<?php namespace Admin\Services\Cache;
/**
 * 
**/
use Admin\Services\Driver;
/**
 * 
**/
class Cache extends Driver
{
	protected $valid_drivers = [
		'apc',
		'dummy',
		'file',
		'memcached',
		'redis',
		'wincache'
	];

	protected $_cache_path = null;
	protected $_adapter = 'dummy';
	protected $_backup_driver = 'dummy';
	public $key_prefix = '';

	public function __construct($config = [])
	{
		isset($config['adapter']) && $this->_adapter = $config['adapter'];
		isset($config['backup']) && $this->_backup_driver = $config['backup'];
		isset($config['key_prefix']) && $this->key_prefix = $config['key_prefix'];

		if ( ! $this->is_supported($this->_adapter))
		{
			if ( ! $this->is_supported($this->_backup_driver))
			{
				\Admin\Core\Error::logMessage('error', 'Cache adapter "'.$this->_adapter.'" and backup "'.$this->_backup_driver.'" are both unavailable. Cache is now using "Dummy" adapter.');
				$this->_adapter = 'dummy';
			}
			else
			{
				\Admin\Core\Error::logMessage('debug', 'Cache adapter "'.$this->_adapter.'" is unavailable. Falling back to "'.$this->_backup_driver.'" backup adapter.');
				$this->_adapter = $this->_backup_driver;
			}
		}
	}

	public function get($id)
	{
		return $this->{$this->_adapter}->get((string)$this->key_prefix.$id);
	}

	public function save($id, $data, $ttl = 60, $raw = false)
	{
		return $this->{$this->_adapter}->save((string)$this->key_prefix.$id, $data, $ttl, $raw);
	}

	public function delete($id)
	{
		return $this->{$this->_adapter}->delete((string)$this->key_prefix.$id);
	}

	public function increment($id, $offset = 1)
	{
		return $this->{$this->_adapter}->increment((string)$this->key_prefix.$id, $offset);
	}

	public function decrement($id, $offset = 1)
	{
		return $this->{$this->_adapter}->decrement((string)$this->key_prefix.$id, $offset);
	}

	public function clean()
	{
		return $this->{$this->_adapter}->clean();
	}

	public function cache_info($type = 'user')
	{
		return $this->{$this->_adapter}->cache_info($type);
	}

	public function get_metadata($id)
	{
		return $this->{$this->_adapter}->get_metadata((string)$this->key_prefix.$id);
	}

	public function is_supported($driver)
	{
		static $support;
		if ( ! isset($support[$driver]))
		{
			$support[$driver] = $this->{$driver}->is_supported();
		}
		return $support[$driver];
	}
}
