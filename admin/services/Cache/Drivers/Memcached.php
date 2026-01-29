<?php namespace Admin\Services\Cache\Drivers;

use Admin\Services\Driver;
use Memcached as NativeMemcached;
use Memcache as NativeMemcache;

class Memcached extends Driver {
	protected $_memcached;
	protected $_config = [
		'default' => [
			'host'		=> '127.0.0.1',
			'port'		=> 11211,
			'weight'	=> 1
		]
	];

	public function __construct()
	{
		$mta =& \Admin\Core\Controller::getInstance();
		$defaults = $this->_config['default'];
		if ($mta->config->load('memcached', true, true))
		{
			$this->_config = $mta->config->config['memcached'];
		}

		if (class_exists('Memcached', false))
		{
			$this->_memcached = new NativeMemcached();
		}
		elseif (class_exists('Memcache', false))
		{
			$this->_memcached = new NativeMemcache();
		}
		else
		{
			\Admin\Core\Error::logMessage('error', 'Cache: Failed to create Memcache(d) object; extension not loaded?');
			return;
		}

		foreach ($this->_config as $cache_server)
		{
			isset($cache_server['hostname']) OR $cache_server['hostname'] = $defaults['host'];
			isset($cache_server['port']) OR $cache_server['port'] = $defaults['port'];
			isset($cache_server['weight']) OR $cache_server['weight'] = $defaults['weight'];

			if ($this->_memcached instanceof NativeMemcache)
			{
				$this->_memcached->addServer(
					(string)$cache_server['hostname'],
					(int)$cache_server['port'],
					true,
					(int)$cache_server['weight']
				);
			}
			elseif ($this->_memcached instanceof NativeMemcached)
			{
				$this->_memcached->addServer(
					(string)$cache_server['hostname'],
					(int)$cache_server['port'],
					(int)$cache_server['weight']
				);
			}
		}
	}

	public function get($id)
	{
		$data = $this->_memcached->get((string)$id);
		return is_array($data) ? $data[0] : $data;
	}

	public function save($id, $data, $ttl = 60, $raw = false)
	{
		if ($raw !== true)
		{
			$data = [$data, time(), $ttl];
		}

		if ($this->_memcached instanceof NativeMemcached)
		{
			return $this->_memcached->set((string)$id, $data, (int)$ttl);
		}
		elseif ($this->_memcached instanceof NativeMemcache)
		{
			return $this->_memcached->set((string)$id, $data, 0, (int)$ttl);
		}

		return false;
	}

	public function delete($id)
	{
		return $this->_memcached->delete((string)$id);
	}

	public function increment($id, $offset = 1)
	{
		if (($result = $this->_memcached->increment((string)$id, (int)$offset)) === false)
		{
			return $this->_memcached->add((string)$id, (int)$offset) ? (int)$offset : false;
		}
		return $result;
	}

	public function decrement($id, $offset = 1)
	{
		if (($result = $this->_memcached->decrement((string)$id, (int)$offset)) === false)
		{
			return $this->_memcached->add((string)$id, 0) ? 0 : false;
		}
		return $result;
	}

	public function clean()
	{
		return $this->_memcached->flush();
	}

	public function cache_info()
	{
		return $this->_memcached->getStats();
	}

	public function get_metadata($id)
	{
		$stored = $this->_memcached->get((string)$id);
		if ( ! is_array($stored) OR count($stored) !== 3)
		{
			return false;
		}

		list($data, $time, $ttl) = $stored;
		return [
			'expire'	=> $time + $ttl,
			'mtime'		=> $time,
			'data'		=> $data
		];
	}

	public function is_supported()
	{
		return (extension_loaded('memcached') OR extension_loaded('memcache'));
	}

	public function __destruct()
	{
		if ($this->_memcached instanceof NativeMemcache)
		{
			$this->_memcached->close();
		}
		elseif ($this->_memcached instanceof NativeMemcached && method_exists($this->_memcached, 'quit'))
		{
			$this->_memcached->quit();
		}
	}
}
