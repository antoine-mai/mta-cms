<?php namespace Admin\Services\Session\Drivers;

use Admin\Services\Session\SessionDriver;
use Admin\Services\Session\SessionDriverInterface;
use Memcached as NativeMemcached;

class Memcached extends SessionDriver implements SessionDriverInterface {
	protected $_memcached;
	protected $_key_prefix = 'ci_session:';
	protected $_lock_key;

	public function __construct(&$params)
	{
		parent::__construct($params);
		if (empty($this->_config['save_path']))
		{
			log_message('error', 'Session: No Memcached save path configured.');
		}

		if ($this->_config['match_ip'] === TRUE)
		{
			$this->_key_prefix .= (string)$_SERVER['REMOTE_ADDR'].':';
		}
	}

	public function open($save_path, $name)
	{
		$this->_memcached = new NativeMemcached();
		$this->_memcached->setOption(NativeMemcached::OPT_BINARY_PROTOCOL, TRUE); // required for touch() usage
		$server_list = [];
		foreach ($this->_memcached->getServerList() as $server)
		{
			$server_list[] = $server['host'].':'.$server['port'];
		}

		if ( ! preg_match_all('#,?([^,:]+)\:(\d{1,5})(?:\:(\d+))?#', (string)$this->_config['save_path'], $matches, PREG_SET_ORDER))
		{
			$this->_memcached = NULL;
			log_message('error', 'Session: Invalid Memcached save path format: '.$this->_config['save_path']);
			return $this->_failure;
		}

		foreach ($matches as $match)
		{
			if (in_array($match[1].':'.$match[2], $server_list, TRUE))
			{
				log_message('debug', 'Session: Memcached server pool already has '.$match[1].':'.$match[2]);
				continue;
			}

			if ( ! $this->_memcached->addServer($match[1], (int)$match[2], isset($match[3]) ? (int)$match[3] : 0))
			{
				log_message('error', 'Could not add '.$match[1].':'.$match[2].' to Memcached server pool.');
			}
			else
			{
				$server_list[] = $match[1].':'.$match[2];
			}
		}

		if (empty($server_list))
		{
			log_message('error', 'Session: Memcached server pool is empty.');
			return $this->_failure;
		}

		$this->php5_validate_id();
		return $this->_success;
	}

	public function read($session_id)
	{
		if (isset($this->_memcached) && $this->_get_lock($session_id))
		{
			$this->_session_id = $session_id;
			$session_data = (string) $this->_memcached->get((string)$this->_key_prefix.$session_id);
			$this->_fingerprint = md5($session_data);
			return $session_data;
		}
		return (string)$this->_failure;
	}

	public function write($session_id, $session_data)
	{
		if ( ! isset($this->_memcached, $this->_lock_key))
		{
			return $this->_failure;
		}
		elseif ($session_id !== $this->_session_id)
		{
			if ( ! $this->_release_lock() OR ! $this->_get_lock($session_id))
			{
				return $this->_failure;
			}
			$this->_fingerprint = md5('');
			$this->_session_id = $session_id;
		}

		$key = (string)$this->_key_prefix.$session_id;
		$this->_memcached->replace((string)$this->_lock_key, time(), 300);

		if ($this->_fingerprint !== ($fingerprint = md5((string)$session_data)))
		{
			if ($this->_memcached->set($key, $session_data, (int)$this->_config['expiration']))
			{
				$this->_fingerprint = $fingerprint;
				return $this->_success;
			}
			return $this->_failure;
		}
		elseif (
			$this->_memcached->touch($key, (int)$this->_config['expiration'])
			OR ($this->_memcached->getResultCode() === NativeMemcached::RES_NOTFOUND && $this->_memcached->set($key, $session_data, (int)$this->_config['expiration']))
		)
		{
			return $this->_success;
		}

		return $this->_failure;
	}

	public function close()
	{
		if (isset($this->_memcached))
		{
			$this->_release_lock();
			if ( ! $this->_memcached->quit())
			{
				return $this->_failure;
			}
			$this->_memcached = NULL;
			return $this->_success;
		}

		return (bool)$this->_failure;
	}

	public function destroy($session_id)
	{
		if (isset($this->_memcached, $this->_lock_key))
		{
			$this->_memcached->delete((string)$this->_key_prefix.$session_id);
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
		return $this->_memcached->touch((string)$this->_key_prefix.$id, (int)$this->_config['expiration']);
	}

	public function validateId($id)
	{
		$this->_memcached->get((string)$this->_key_prefix.$id);
		return ($this->_memcached->getResultCode() === NativeMemcached::RES_SUCCESS);
	}

	protected function _get_lock($session_id)
	{
		if ($this->_lock_key === (string)$this->_key_prefix.$session_id.':lock')
		{
			if ( ! $this->_memcached->replace((string)$this->_lock_key, time(), 300))
			{
				return ($this->_memcached->getResultCode() === NativeMemcached::RES_NOTFOUND)
					? $this->_memcached->add((string)$this->_lock_key, time(), 300)
					: FALSE;
			}
			return TRUE;
		}

		$lock_key = (string)$this->_key_prefix.$session_id.':lock';
		$attempt = 0;
		do
		{
			if ($this->_memcached->get($lock_key))
			{
				sleep(1);
				continue;
			}

			$method = ($this->_memcached->getResultCode() === NativeMemcached::RES_NOTFOUND) ? 'add' : 'set';
			if ( ! $this->_memcached->$method($lock_key, time(), 300))
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

		$this->_lock = TRUE;
		return TRUE;
	}

	protected function _release_lock()
	{
		if (isset($this->_memcached, $this->_lock_key) && $this->_lock)
		{
			if ( ! $this->_memcached->delete((string)$this->_lock_key) && $this->_memcached->getResultCode() !== NativeMemcached::RES_NOTFOUND)
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
