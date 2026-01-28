<?php
namespace Admin\Core;
defined('ADMIN_ROOT') OR exit('No direct script access allowed');
#[\AllowDynamicProperties]
class Input {
	protected $ip_address = FALSE;
	protected $_allow_get_array = TRUE;
	protected $_standardize_newlines;
	protected $headers = [];
	protected $_raw_input_stream;
	protected $_input_stream;
    // Security and Utf8 references kept if needed for other things, but XSS/CSRF cleaning is being reduced
	protected $security;
	protected $uni;
	public function __construct()
	{
		$this->_allow_get_array		= (\Admin\Core\Common::config_item('allow_get_array') !== FALSE);
		$this->_standardize_newlines	= (bool) \Admin\Core\Common::config_item('standardize_newlines');
		
        // XSS and CSRF configs removed
        
		$this->security =& \Admin\Core\Registry::getInstance('Security', 'core');
		if (UTF8_ENABLED === TRUE)
		{
			$this->uni =& \Admin\Core\Registry::getInstance('Utf8');
		}

        // _sanitize_globals() call removed
        
		// CSRF check removed from here (should be Middleware/Security layer if needed, but requested to remove XSS related stuff)
		/*
		if ($this->_enable_csrf === TRUE && ! \Admin\Core\Common::is_cli())
		{
			$this->security->csrf_verify();
		}
        */
		Error::log_message('info', 'Input Class Initialized');
	}
	protected function _fetch_from_array(&$array, $index = NULL)
	{
		isset($index) OR $index = array_keys($array);
		if (is_array($index))
		{
			$output = [];
			foreach ($index as $key)
			{
				$output[$key] = $this->_fetch_from_array($array, $key);
			}
			return $output;
		}
		if (isset($array[$index]))
		{
			$value = $array[$index];
		}
		elseif (($count = preg_match_all('/(?:^[^\[]+)|\[[^]]*\]/', $index, $matches)) > 1) // Does the index contain array notation
		{
			$value = $array;
			for ($i = 0; $i < $count; $i++)
			{
				$key = trim($matches[0][$i], '[]');
				if ($key === '') // Empty notation will return the value as array
				{
					break;
				}
				if (isset($value[$key]))
				{
					$value = $value[$key];
				}
				else
				{
					return NULL;
				}
			}
		}
		else
		{
			return NULL;
		}
		return $value;
	}
	public function get($index = NULL)
	{
		return $this->_fetch_from_array($_GET, $index);
	}
	public function post($index = NULL)
	{
		return $this->_fetch_from_array($_POST, $index);
	}
	public function post_get($index)
	{
		return isset($_POST[$index])
			? $this->post($index)
			: $this->get($index);
	}
	public function get_post($index)
	{
		return isset($_GET[$index])
			? $this->get($index)
			: $this->post($index);
	}
	public function cookie($index = NULL)
	{
		return $this->_fetch_from_array($_COOKIE, $index);
	}
	public function server($index)
	{
		return $this->_fetch_from_array($_SERVER, $index);
	}
	public function input_stream($index = NULL)
	{
		if ( ! is_array($this->_input_stream))
		{
			parse_str($this->raw_input_stream, $this->_input_stream);
			is_array($this->_input_stream) OR $this->_input_stream = [];
		}
		return $this->_fetch_from_array($this->_input_stream, $index);
	}
	public function set_cookie($name, $value = '', $expire = '', $domain = '', $path = '/', $prefix = '', $secure = NULL, $httponly = NULL, $samesite = NULL)
	{
		if (is_array($name))
		{
			foreach (['value', 'expire', 'domain', 'path', 'prefix', 'secure', 'httponly', 'name', 'samesite'] as $item)
			{
				if (isset($name[$item]))
				{
					$$item = $name[$item];
				}
			}
		}
		if ($prefix === '' && \Admin\Core\Common::config_item('cookie_prefix') !== '')
		{
			$prefix = \Admin\Core\Common::config_item('cookie_prefix');
		}
		if ($domain == '' && \Admin\Core\Common::config_item('cookie_domain') != '')
		{
			$domain = \Admin\Core\Common::config_item('cookie_domain');
		}
		if ($path === '/' && \Admin\Core\Common::config_item('cookie_path') !== '/')
		{
			$path = \Admin\Core\Common::config_item('cookie_path');
		}
		$secure = ($secure === NULL && \Admin\Core\Common::config_item('cookie_secure') !== NULL)
			? (bool) \Admin\Core\Common::config_item('cookie_secure')
			: (bool) $secure;
		$httponly = ($httponly === NULL && \Admin\Core\Common::config_item('cookie_httponly') !== NULL)
			? (bool) \Admin\Core\Common::config_item('cookie_httponly')
			: (bool) $httponly;
		if ( ! is_numeric($expire))
		{
			$expire = time() - 86500;
		}
		else
		{
			$expire = ($expire > 0) ? time() + $expire : 0;
		}
		isset($samesite) OR $samesite = \Admin\Core\Common::config_item('cookie_samesite');
		if (isset($samesite))
		{
			$samesite = ucfirst(strtolower($samesite));
			in_array($samesite, ['Lax', 'Strict', 'None'], TRUE) OR $samesite = 'Lax';
		}
		else
		{
			$samesite = 'Lax';
		}
		if ($samesite === 'None' && ! $secure)
		{
			Error::log_message('error', $name.' cookie sent with SameSite=None, but without Secure attribute.');
		}
		if ( ! \Admin\Core\Common::is_php('7.3'))
		{
			$maxage = $expire - time();
			if ($maxage < 1)
			{
				$maxage = 0;
			}
			$cookie_header = 'Set-Cookie: '.$prefix.$name.'='.rawurlencode($value);
			$cookie_header .= ($expire === 0 ? '' : '; Expires='.gmdate('D, d-M-Y H:i:s T', $expire)).'; Max-Age='.$maxage;
			$cookie_header .= '; Path='.$path.($domain !== '' ? '; Domain='.$domain : '');
			$cookie_header .= ($secure ? '; Secure' : '').($httponly ? '; HttpOnly' : '').'; SameSite='.$samesite;
			header($cookie_header);
			return;
		}
		$setcookie_options = [
			'expires' => $expire,
			'path' => $path,
			'domain' => $domain,
			'secure' => $secure,
			'httponly' => $httponly,
			'samesite' => $samesite,
		];
		setcookie($prefix.$name, $value, $setcookie_options);
	}



}
