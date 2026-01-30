<?php namespace Root\Services\Cache\Drivers;

use Root\Services\Driver;
use Redis as NativeRedis;
use RedisException;

class Redis extends Driver
{
	protected static $_default_config = [
		'socket_type' => 'tcp',
		'host' => '127.0.0.1',
		'password' => null,
		'port' => 6379,
		'timeout' => 0
	];

	protected $_redis;
	protected $_serialized = [];
	protected static $_delete_name;
	protected static $_sRemove_name;

	public function __construct()
	{
		if ( ! $this->is_supported())
		{
			\Root\Core\Error::error_log( 'Cache: Failed to create Redis object; extension not loaded?');
			return;
		}

		if ( ! isset(static::$_delete_name, static::$_sRemove_name))
		{
			if (version_compare((string)phpversion('redis'), '5', '>='))
			{
				static::$_delete_name  = 'del';
				static::$_sRemove_name = 'sRem';
			}
			else
			{
				static::$_delete_name  = 'delete';
				static::$_sRemove_name = 'sRemove';
			}
		}

		$mta =& \Root\Core\Controller::getInstance();
		if ($mta->config->load('redis', true, true))
		{
			$config = array_merge(self::$_default_config, $mta->config->item('redis'));
		}
		else
		{
			$config = self::$_default_config;
		}

		$this->_redis = new NativeRedis();
		try
		{
			if (isset($config['socket_type']) && $config['socket_type'] === 'unix')
			{
				$success = $this->_redis->connect((string)$config['socket']);
			}
			else // tcp socket
			{
				$success = $this->_redis->connect((string)$config['host'], (int)$config['port'], (float)$config['timeout']);
			}

			if ( ! $success)
			{
				\Root\Core\Error::error_log( 'Cache: Redis connection failed. Check your configuration.');
			}

			if (isset($config['password']) && ! $this->_redis->auth((string)$config['password']))
			{
				\Root\Core\Error::error_log( 'Cache: Redis authentication failed.');
			}
		}
		catch (RedisException $e)
		{
			\Root\Core\Error::error_log( 'Cache: Redis connection refused ('.$e->getMessage().')');
		}
	}

	public function get($key)
	{
		$value = $this->_redis->get((string)$key);
		if ($value !== false && $this->_redis->sIsMember('_mta_redis_serialized', (string)$key))
		{
			return unserialize((string)$value);
		}
		return $value;
	}

	public function save($id, $data, $ttl = 60, $raw = false)
	{
		if (is_array($data) OR is_object($data))
		{
			if ( ! $this->_redis->sIsMember('_mta_redis_serialized', (string)$id) && ! $this->_redis->sAdd('_mta_redis_serialized', (string)$id))
			{
				return false;
			}
			isset($this->_serialized[$id]) OR $this->_serialized[$id] = true;
			$data = serialize($data);
		}
		else
		{
			$this->_redis->{static::$_sRemove_name}('_mta_redis_serialized', (string)$id);
		}
		return $this->_redis->set((string)$id, $data, (int)$ttl);
	}

	public function delete($key)
	{
		if ($this->_redis->{static::$_delete_name}((string)$key) !== 1)
		{
			return false;
		}
		$this->_redis->{static::$_sRemove_name}('_mta_redis_serialized', (string)$key);
		return true;
	}

	public function increment($id, $offset = 1)
	{
		return $this->_redis->incrBy((string)$id, (int)$offset);
	}

	public function decrement($id, $offset = 1)
	{
		return $this->_redis->decrBy((string)$id, (int)$offset);
	}

	public function clean()
	{
		return $this->_redis->flushDB();
	}

	public function cache_info($type = null)
	{
		return $this->_redis->info();
	}

	public function get_metadata($key)
	{
		$value = $this->get($key);
		if ($value !== false)
		{
			return [
				'expire' => time() + $this->_redis->ttl((string)$key),
				'data' => $value
			];
		}
		return false;
	}

	public function is_supported()
	{
		return extension_loaded('redis');
	}

	public function __destruct()
	{
		if ($this->_redis)
		{
			$this->_redis->close();
		}
	}
}
