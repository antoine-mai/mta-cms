<?php namespace Admin\Services\Session\Drivers;
/**
 * 
**/
use Admin\Services\Session\SessionDriverInterface;
use Admin\Services\Session\SessionDriver;
use Redis as NativeRedis;
use RedisException;
/**
 * 
**/
class Redis extends SessionDriver implements SessionDriverInterface
{
	protected $_redis;
	protected $_key_prefix = 'mta_cms_admin_session:';
	protected $_lock_key;
	protected $_key_exists = FALSE;
	protected $_setTimeout_name;
	protected $_delete_name;
	protected $_ping_success;

	public function __construct(&$params)
	{
		parent::__construct($params);
		if (version_compare((string)phpversion('redis'), '5', '>='))
		{
			$this->_setTimeout_name = 'expire';
			$this->_delete_name = 'del';
			$this->_ping_success = TRUE;
		}
		else
		{
			$this->_setTimeout_name = 'setTimeout';
			$this->_delete_name = 'delete';
			$this->_ping_success = '+PONG';
		}

		if (empty($this->_config['save_path']))
		{
			log_message('error', 'Session: No Redis save path configured.');
		}
		elseif (preg_match('#(?:tcp://)?([^:?]+)(?:\:(\d+))?(\?.+)?#', (string)$this->_config['save_path'], $matches))
		{
			isset($matches[3]) OR $matches[3] = '';
			$this->_config['save_path'] = [
				'host' => $matches[1],
				'port' => empty($matches[2]) ? NULL : $matches[2],
				'password' => preg_match('#auth=([^\s&]+)#', $matches[3], $match) ? $match[1] : NULL,
				'database' => preg_match('#database=(\d+)#', $matches[3], $match) ? (int) $match[1] : NULL,
				'timeout' => preg_match('#timeout=(\d+\.\d+)#', $matches[3], $match) ? (float) $match[1] : NULL
			];
			preg_match('#prefix=([^\s&]+)#', $matches[3], $match) && $this->_key_prefix = $match[1];
		}
		else
		{
			log_message('error', 'Session: Invalid Redis save path format: '.$this->_config['save_path']);
		}

		if ($this->_config['match_ip'] === TRUE)
		{
			$this->_key_prefix .= (string)$_SERVER['REMOTE_ADDR'].':';
		}
	}

	public function open($save_path, $name)
	{
		if (empty($this->_config['save_path']))
		{
			return $this->_failure;
		}

		$redis = new NativeRedis();
		if ( ! $redis->connect((string)$this->_config['save_path']['host'], (int)$this->_config['save_path']['port'], (float)$this->_config['save_path']['timeout']))
		{
			log_message('error', 'Session: Unable to connect to Redis with the configured settings.');
		}
		elseif (isset($this->_config['save_path']['password']) && ! $redis->auth((string)$this->_config['save_path']['password']))
		{
			log_message('error', 'Session: Unable to authenticate to Redis instance.');
		}
		elseif (isset($this->_config['save_path']['database']) && ! $redis->select((int)$this->_config['save_path']['database']))
		{
			log_message('error', 'Session: Unable to select Redis database with index '.$this->_config['save_path']['database']);
		}
		else
		{
			$this->_redis = $redis;
			$this->php5_validate_id();
			return $this->_success;
		}

		return $this->_failure;
	}

	public function read($session_id)
	{
		if (isset($this->_redis) && $this->_get_lock($session_id))
		{
			$this->_session_id = $session_id;
			$session_data = $this->_redis->get((string)$this->_key_prefix.$session_id);
			is_string($session_data)
				? $this->_key_exists = TRUE
				: $session_data = '';
			$this->_fingerprint = md5($session_data);
			return $session_data;
		}
		return (string)$this->_failure;
	}

	public function write($session_id, $session_data)
	{
		if ( ! isset($this->_redis, $this->_lock_key))
		{
			return $this->_failure;
		}
		elseif ($session_id !== $this->_session_id)
		{
			if ( ! $this->_release_lock() OR ! $this->_get_lock($session_id))
			{
				return $this->_failure;
			}
			$this->_key_exists = FALSE;
			$this->_session_id = $session_id;
		}

		$this->_redis->{$this->_setTimeout_name}((string)$this->_lock_key, 300);
		if ($this->_fingerprint !== ($fingerprint = md5((string)$session_data)) OR $this->_key_exists === FALSE)
		{
			if ($this->_redis->set((string)$this->_key_prefix.$session_id, $session_data, (int)$this->_config['expiration']))
			{
				$this->_fingerprint = $fingerprint;
				$this->_key_exists = TRUE;
				return $this->_success;
			}
			return $this->_failure;
		}

		return ($this->_redis->{$this->_setTimeout_name}((string)$this->_key_prefix.$session_id, (int)$this->_config['expiration']))
			? $this->_success
			: $this->_failure;
	}

	public function close()
	{
		if (isset($this->_redis))
		{
			try {
				if ($this->_redis->ping() === $this->_ping_success)
				{
					$this->_release_lock();
					if ($this->_redis->close() === FALSE)
					{
						return $this->_failure;
					}
				}
			}
			catch (RedisException $e)
			{
				log_message('error', 'Session: Got RedisException on close(): '.$e->getMessage());
			}
			$this->_redis = NULL;
			return $this->_success;
		}

		return (bool)$this->_success;
	}

	public function destroy($session_id)
	{
		if (isset($this->_redis, $this->_lock_key))
		{
			if (($result = $this->_redis->{$this->_delete_name}((string)$this->_key_prefix.$session_id)) !== 1)
			{
				log_message('debug', 'Session: Redis::'.$this->_delete_name.'() expected to return 1, got '.var_export($result, TRUE).' instead.');
			}
			$this->_cookie_destroy();
			return $this->_success;
		}

		return $this->_failure;
	}

	public function gc($maxlifetime)
	{
		return $this->_success;
	}

	public function updateTimestamp($id, $data)
	{
		return $this->_redis->{$this->_setTimeout_name}((string)$this->_key_prefix.$id, (int)$this->_config['expiration']);
	}

	public function validateId($id)
	{
		return (bool) $this->_redis->exists((string)$this->_key_prefix.$id);
	}

	protected function _get_lock($session_id)
	{
		if ($this->_lock_key === (string)$this->_key_prefix.$session_id.':lock')
		{
			return $this->_redis->{$this->_setTimeout_name}((string)$this->_lock_key, 300);
		}

		$lock_key = (string)$this->_key_prefix.$session_id.':lock';
		$attempt = 0;
		$ttl = 0;
		do
		{
			if (($ttl = (int)$this->_redis->ttl($lock_key)) > 0)
			{
				sleep(1);
				continue;
			}

			if ($ttl === -2 && ! $this->_redis->set($lock_key, (string)time(), ['nx', 'ex' => 300]))
			{
				sleep(1);
				continue;
			}
			elseif ( ! $this->_redis->setex($lock_key, 300, (string)time()))
			{
				log_message('error', 'Session: Error while trying to obtain lock for '.$this->_key_prefix.$session_id);
				return FALSE;
			}

			$this->_lock_key = $lock_key;
			break;
		}
		while (++$attempt < 30);

		if ($attempt === 30)
		{
			log_message('error', 'Session: Unable to obtain lock for '.$this->_key_prefix.$session_id.' after 30 attempts, aborting.');
			return FALSE;
		}
		elseif ($ttl === -1)
		{
			log_message('debug', 'Session: Lock for '.$this->_key_prefix.$session_id.' had no TTL, overriding.');
		}

		$this->_lock = TRUE;
		return TRUE;
	}

	protected function _release_lock()
	{
		if (isset($this->_redis, $this->_lock_key) && $this->_lock)
		{
			if ( ! $this->_redis->{$this->_delete_name}((string)$this->_lock_key))
			{
				log_message('error', 'Session: Error while trying to free lock for '.$this->_lock_key);
				return FALSE;
			}
			$this->_lock_key = NULL;
			$this->_lock = FALSE;
		}

		return TRUE;
	}
}
