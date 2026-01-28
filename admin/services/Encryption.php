<?php namespace Admin\Services;

class Encryption {
	protected $_cipher = 'aes-128';
	protected $_mode = 'cbc';
	protected $_handle;
	protected $_key;
	protected $_modes = [
		'openssl' => [
			'cbc' => 'cbc',
			'ecb' => 'ecb',
			'ofb' => 'ofb',
			'cfb' => 'cfb',
			'cfb8' => 'cfb8',
			'ctr' => 'ctr',
			'stream' => '',
			'xts' => 'xts'
		]
	];
	protected $_digests = [
		'sha224' => 28,
		'sha256' => 32,
		'sha384' => 48,
		'sha512' => 64
	];

	public function __construct(array $params = [])
	{
		if ( ! extension_loaded('openssl'))
		{
			show_error('Encryption: Unable to find an available encryption driver.');
		}
		$this->initialize($params);
		if ( ! isset($this->_key) && ($key = config_item('encryption_key')))
		{
			$this->_key = $key;
		}
		log_message('info', 'Encryption Class Initialized');
	}

	public function initialize(array $params)
	{
		empty($params['cipher']) && $params['cipher'] = $this->_cipher;
		empty($params['key']) OR $this->_key = $params['key'];
		
		if ( ! empty($params['cipher']))
		{
			$params['cipher'] = strtolower((string)$params['cipher']);
			$this->_cipher_alias($params['cipher']);
			$this->_cipher = $params['cipher'];
		}
		if ( ! empty($params['mode']))
		{
			$params['mode'] = strtolower((string)$params['mode']);
			if (isset($this->_modes['openssl'][$params['mode']]))
			{
				$this->_mode = $this->_modes['openssl'][$params['mode']];
			}
		}
		if (isset($this->_cipher, $this->_mode))
		{
			$handle = empty($this->_mode) ? $this->_cipher : $this->_cipher.'-'.$this->_mode;
			if ( ! in_array($handle, openssl_get_cipher_methods(), TRUE))
			{
				$this->_handle = NULL;
				log_message('error', 'Encryption: Unable to initialize OpenSSL with method '.strtoupper((string)$handle).'.');
			}
			else
			{
				$this->_handle = $handle;
			}
		}
		return $this;
	}

	public function create_key($length)
	{
		return random_bytes((int) $length);
	}

	public function encrypt($data, array $params = NULL)
	{
		if (($params = $this->_get_params($params)) === FALSE)
		{
			return FALSE;
		}
		isset($params['key']) OR $params['key'] = $this->hkdf((string)$this->_key, 'sha512', NULL, strlen((string)$this->_key), 'encryption');
		
		if (empty($params['handle']))
		{
			return FALSE;
		}
		$iv = ($iv_size = openssl_cipher_iv_length($params['handle'])) ? $this->create_key($iv_size) : '';
		$data = openssl_encrypt((string)$data, $params['handle'], (string)$params['key'], 1, $iv);
		if ($data === FALSE)
		{
			return FALSE;
		}
		$data = $iv.$data;

		$params['base64'] && $data = base64_encode($data);
		if (isset($params['hmac_digest']))
		{
			isset($params['hmac_key']) OR $params['hmac_key'] = $this->hkdf((string)$this->_key, 'sha512', NULL, NULL, 'authentication');
			return hash_hmac($params['hmac_digest'], $data, (string)$params['hmac_key'], ! $params['base64']).$data;
		}
		return $data;
	}

	public function decrypt($data, array $params = NULL)
	{
		if (($params = $this->_get_params($params)) === FALSE)
		{
			return FALSE;
		}
		if (isset($params['hmac_digest']))
		{
			$digest_size = ($params['base64']) ? $this->_digests[$params['hmac_digest']] * 2 : $this->_digests[$params['hmac_digest']];
			if (strlen((string)$data) <= $digest_size)
			{
				return FALSE;
			}
			$hmac_input = substr((string)$data, 0, $digest_size);
			$data = substr((string)$data, $digest_size);
			isset($params['hmac_key']) OR $params['hmac_key'] = $this->hkdf((string)$this->_key, 'sha512', NULL, NULL, 'authentication');
			$hmac_check = hash_hmac($params['hmac_digest'], (string)$data, (string)$params['hmac_key'], ! $params['base64']);
			if (!hash_equals($hmac_input, $hmac_check))
			{
				return FALSE;
			}
		}
		if ($params['base64'])
		{
			$data = base64_decode((string)$data);
		}
		isset($params['key']) OR $params['key'] = $this->hkdf((string)$this->_key, 'sha512', NULL, strlen((string)$this->_key), 'encryption');
		
		if ($iv_size = openssl_cipher_iv_length($params['handle']))
		{
			$iv = substr((string)$data, 0, $iv_size);
			$data = substr((string)$data, $iv_size);
		}
		else
		{
			$iv = '';
		}
		return openssl_decrypt((string)$data, $params['handle'], (string)$params['key'], 1, $iv);
	}

	protected function _get_params($params)
	{
		if (empty($params))
		{
			return isset($this->_cipher, $this->_mode, $this->_key, $this->_handle)
				? [
					'handle' => $this->_handle,
					'cipher' => $this->_cipher,
					'mode' => $this->_mode,
					'key' => NULL,
					'base64' => TRUE,
					'hmac_digest' => 'sha512',
					'hmac_key' => NULL
				]
				: FALSE;
		}
		$params = [
			'handle' => NULL,
			'cipher' => $params['cipher'],
			'mode' => $params['mode'],
			'key' => $params['key'],
			'base64' => isset($params['raw_data']) ? ! $params['raw_data'] : FALSE,
			'hmac_digest' => $params['hmac_digest'] ?? 'sha512',
			'hmac_key' => $params['hmac_key']
		];
		$this->_cipher_alias($params['cipher']);
		$params['handle'] = ($params['cipher'] !== $this->_cipher OR $params['mode'] !== $this->_mode)
			? $params['cipher'].'-'.$params['mode']
			: $this->_handle;
		return $params;
	}

	protected function _cipher_alias(&$cipher)
	{
		static $dictionary = [
			'rijndael-128' => 'aes-128',
			'tripledes' => 'des-ede3',
			'blowfish' => 'bf',
			'cast-128' => 'cast5',
			'arcfour' => 'rc4-40',
			'rc4' => 'rc4-40'
		];
		if (isset($dictionary[$cipher]))
		{
			$cipher = $dictionary[$cipher];
		}
	}

	public function hkdf($key, $digest = 'sha512', $salt = NULL, $length = NULL, $info = '')
	{
		if ( ! isset($this->_digests[$digest]))
		{
			return FALSE;
		}
		if (empty($length) OR ! is_int($length))
		{
			$length = $this->_digests[$digest];
		}
		$salt OR $salt = str_repeat("\0", $this->_digests[$digest]);
		return hash_hkdf($digest, (string)$key, $length, (string)$info, (string)$salt);
	}

	public function __get($key)
	{
		if ($key === 'mode')
		{
			return array_search($this->_mode, $this->_modes['openssl'], TRUE);
		}
		elseif (in_array($key, ['cipher', 'digests'], TRUE))
		{
			return $this->{'_'.$key};
		}
		return NULL;
	}
}
