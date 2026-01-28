<?php namespace Admin\Core;
/**
 * 
**/
#[\AllowDynamicProperties]
class Lang
{
	public $language =	[];
	public $isLoaded =	[];
	public function __construct()
	{
		\Admin\Core\Error::logMessage('info', 'Language Class Initialized');
	}
	public function load($langfile, $idiom = '', $return = false, $add_suffix = true, $alt_path = '')
	{
		if (is_array($langfile))
		{
			foreach ($langfile as $value)
			{
				$this->load($value, $idiom, $return, $add_suffix, $alt_path);
			}
			return;
		}
		$langfile = str_replace('.php', '', $langfile);
		if ($add_suffix === true)
		{
			$langfile = preg_replace('/_lang$/', '', $langfile).'_lang';
		}
		$langfile .= '.php';
		if (empty($idiom) OR ! preg_match('/^[a-z_-]+$/i', $idiom))
		{
			$config =& get_config();
			$idiom = empty($config['language']) ? 'english' : $config['language'];
		}
		if ($return === false && isset($this->isLoaded[$langfile]) && $this->isLoaded[$langfile] === $idiom)
		{
			return;
		}
		$basepath = ADMIN_ROOT.'language/'.$idiom.'/'.$langfile;
		if (($found = file_exists($basepath)) === true)
		{
			include($basepath);
		}
		if ($alt_path !== '')
		{
			$alt_path .= 'language/'.$idiom.'/'.$langfile;
			if (file_exists($alt_path))
			{
				include($alt_path);
				$found = true;
			}
		}
		else
		{
			foreach (getInstance()->load->get_package_paths(true) as $package_path)
			{
				$package_path .= 'language/'.$idiom.'/'.$langfile;
				if ($basepath !== $package_path && file_exists($package_path))
				{
					include($package_path);
					$found = true;
					break;
				}
			}
		}
		if ($found !== true)
		{
			\Admin\Core\Error::showError('Unable to load the requested language file: language/'.$idiom.'/'.$langfile);
		}
		if ( ! isset($lang) OR ! is_array($lang))
		{
			\Admin\Core\Error::logMessage('error', 'Language file contains no data: language/'.$idiom.'/'.$langfile);
			if ($return === true)
			{
				return [];
			}
			return;
		}
		if ($return === true)
		{
			return $lang;
		}
		$this->isLoaded[$langfile] = $idiom;
		$this->language = array_merge($this->language, $lang);
		\Admin\Core\Error::logMessage('info', 'Language file loaded: language/'.$idiom.'/'.$langfile);
		return true;
	}
	public function line($line, $log_errors = true)
	{
		$value = isset($this->language[$line]) ? $this->language[$line] : false;
		if ($value === false && $log_errors === true)
		{
			\Admin\Core\Error::logMessage('error', 'Could not find the language line "'.$line.'"');
		}
		return $value;
	}
}
