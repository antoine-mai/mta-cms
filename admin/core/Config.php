<?php namespace Admin\Core;
/**
 * 
**/
class Config
{
	public $config = [];
	public $is_loaded =	[];
	public $_config_paths =	[ADMIN_ROOT];
	public function __construct()
	{
		$this->config =& get_config();
		log_message('info', 'Config Class Initialized');
	}
	public function load($file = '', $use_sections = FALSE, $fail_gracefully = FALSE)
	{
		$file = ($file === '') ? 'config' : str_replace('.php', '', $file);
		$loaded = FALSE;
		foreach ($this->_config_paths as $path)
		{
			foreach ([$file] as $location)
			{
				$file_path = ($path === ADMIN_ROOT) ? ROOT_DIR . '/config/admin/'.$location.'.yaml' : $path.'config/'.$location.'.yaml';
				if (in_array($file_path, $this->is_loaded, TRUE))
				{
					return TRUE;
				}
				if ( ! file_exists($file_path))
				{
					continue;
				}
				
                $yaml = new \Admin\Services\Yaml();
                $config = $yaml->parse($file_path);

				if ( ! isset($config) OR ! is_array($config))
				{
					if ($fail_gracefully === TRUE)
					{
						return FALSE;
					}
					show_error('Your '.$file_path.' file does not appear to contain a valid configuration array.');
				}
				if ($use_sections === TRUE)
				{
					$this->config[$file] = isset($this->config[$file])
						? array_merge($this->config[$file], $config)
						: $config;
				}
				else
				{
					$this->config = array_merge($this->config, $config);
				}
				$this->is_loaded[] = $file_path;
				$config = NULL;
				$loaded = TRUE;
				log_message('debug', 'Config file loaded: '.$file_path);
			}
		}
		if ($loaded === TRUE)
		{
			return TRUE;
		}
		elseif ($fail_gracefully === TRUE)
		{
			return FALSE;
		}
		show_error('The configuration file '.$file.'.yaml does not exist.');
	}
	public function item($item, $index = '')
	{
		if ($index == '')
		{
			return isset($this->config[$item]) ? $this->config[$item] : NULL;
		}
		return isset($this->config[$index], $this->config[$index][$item]) ? $this->config[$index][$item] : NULL;
	}
	public function slash_item($item)
	{
		if ( ! isset($this->config[$item]))
		{
			return NULL;
		}
		elseif (trim($this->config[$item]) === '')
		{
			return '';
		}
		return rtrim($this->config[$item], '/').'/';
	}
	public function site_url($uri = '', $protocol = NULL)
	{
		$base_url = $this->slash_item('base_url');
		if (isset($protocol))
		{
			if ($protocol === '')
			{
				$base_url = substr($base_url, strpos($base_url, '//'));
			}
			else
			{
				$base_url = $protocol.substr($base_url, strpos($base_url, '://'));
			}
		}
		if (empty($uri))
		{
			return $base_url.$this->item('index_page');
		}
		$uri = $this->_uri_string($uri);
		if ($this->item('enable_query_strings') === FALSE)
		{
			$suffix = isset($this->config['url_suffix']) ? $this->config['url_suffix'] : '';
			if ($suffix !== '')
			{
				if (($offset = strpos($uri, '?')) !== FALSE)
				{
					$uri = substr($uri, 0, $offset).$suffix.substr($uri, $offset);
				}
				else
				{
					$uri .= $suffix;
				}
			}
			return $base_url.$this->slash_item('index_page').$uri;
		}
		elseif (strpos($uri, '?') === FALSE)
		{
			$uri = '?'.$uri;
		}
		return $base_url.$this->item('index_page').$uri;
	}
	public function base_url($uri = '', $protocol = NULL)
	{
		$base_url = $this->slash_item('base_url');
		if (isset($protocol))
		{
			if ($protocol === '')
			{
				$base_url = substr($base_url, strpos($base_url, '//'));
			}
			else
			{
				$base_url = $protocol.substr($base_url, strpos($base_url, '://'));
			}
		}
		return $base_url.$this->_uri_string($uri);
	}
	protected function _uri_string($uri)
	{
		if ($this->item('enable_query_strings') === FALSE)
		{
			is_array($uri) && $uri = implode('/', $uri);
			return ltrim($uri, '/');
		}
		elseif (is_array($uri))
		{
			return http_build_query($uri);
		}
		return $uri;
	}
	public function system_url()
	{
		$x = explode('/', preg_replace('|/*(.+?)/*$|', '\\1', ADMIN_ROOT));
		return $this->slash_item('base_url').end($x).'/';
	}
	public function set_item($item, $value)
	{
		$this->config[$item] = $value;
	}
}
