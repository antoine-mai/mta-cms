<?php namespace Admin\Core;
/**
 * 
**/
class Loader
{
	protected $_ci_ob_level;
	protected $_ci_view_paths =	[ADMIN_ROOT . 'template/'	=> TRUE];
	protected $_ci_library_paths =	[ADMIN_ROOT, ADMIN_ROOT];
	protected $_ci_helper_paths =	[ADMIN_ROOT, ADMIN_ROOT];
	protected $_ci_cached_vars =	[];
	protected $_ci_classes =	[];
	protected $_ci_helpers =	[];
	protected $_ci_varmap =	[
		'unit_test' => 'unit',
		'user_agent' => 'agent'
	];
	public function __construct()
	{
		$this->_ci_ob_level = ob_get_level();
		$this->_ci_classes =& is_loaded();
		log_message('info', 'Loader Class Initialized');
	}

	public function autoloadPsr4(array $namespaces = [])
	{
		foreach ($namespaces as $prefix => $base_dir)
		{
			$prefix = trim($prefix, '\\') . '\\';
			$base_dir = rtrim($base_dir, DIRECTORY_SEPARATOR) . '/';

			spl_autoload_register(function ($class) use ($prefix, $base_dir) {
				$len = strlen($prefix);
				if (strncmp($prefix, $class, $len) !== 0)
				{
					return;
				}
				
				$relative_class = substr($class, $len);
				$file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
				
				if (file_exists($file))
				{
					require $file;
				}
			});
		}
	}
	public function initialize()
	{
		$this->_ci_autoloader();
	}
	public function is_loaded($class)
	{
		return array_search(ucfirst($class), $this->_ci_classes, TRUE);
	}
	public function library($library, $params = NULL, $object_name = NULL)
	{
		if (empty($library))
		{
			return $this;
		}
		elseif (is_array($library))
		{
			foreach ($library as $key => $value)
			{
				if (is_int($key))
				{
					$this->library($value, $params);
				}
				else
				{
					$this->library($key, $params, $value);
				}
			}
			return $this;
		}
		if ($params !== NULL && ! is_array($params))
		{
			$params = NULL;
		}
		$this->_ci_load_library($library, $params, $object_name);
		return $this;
	}
	public function template($view, $vars = [], $return = FALSE)
	{
		return $this->_ci_load(['_ci_view' => $view, '_ci_vars' => $this->_ci_prepare_view_vars($vars), '_ci_return' => $return]);
	}
	public function file($path, $return = FALSE)
	{
		return $this->_ci_load(['_ci_path' => $path, '_ci_return' => $return]);
	}
	public function vars($vars, $val = '')
	{
		$vars = is_string($vars)
			? [$vars => $val]
			: $this->_ci_prepare_view_vars($vars);
		foreach ($vars as $key => $val)
		{
			$this->_ci_cached_vars[$key] = $val;
		}
		return $this;
	}
	public function clear_vars()
	{
		$this->_ci_cached_vars = [];
		return $this;
	}
	public function get_var($key)
	{
		return isset($this->_ci_cached_vars[$key]) ? $this->_ci_cached_vars[$key] : NULL;
	}
	public function get_vars()
	{
		return $this->_ci_cached_vars;
	}
	public function helper($helpers = [])
	{
		is_array($helpers) OR $helpers = [$helpers];
		foreach ($helpers as &$helper)
		{
			$filename = basename($helper);
			$filepath = ($filename === $helper) ? '' : substr($helper, 0, strlen($helper) - strlen($filename));
			$filename = strtolower(preg_replace('#(_helper)?(\.php)?$#i', '', $filename));
			$helper   = $filepath.$filename;
			if (isset($this->_ci_helpers[$helper]))
			{
				continue;
			}
			$ext_helper = config_item('subclass_prefix').$filename;
			$ext_loaded = FALSE;
			foreach ($this->_ci_helper_paths as $path)
			{
				if (file_exists($path.'services/'.$ext_helper.'.php'))
				{
					include_once($path.'services/'.$ext_helper.'.php');
					$ext_loaded = TRUE;
				}
			}
			if ($ext_loaded === TRUE)
			{
				$base_helper = ADMIN_ROOT.'services/'.$helper.'.php';
				if ( ! file_exists($base_helper))
				{
					show_error('Unable to load the requested file: services/'.$helper.'.php');
				}
				include_once($base_helper);
				$this->_ci_helpers[$helper] = TRUE;
				log_message('info', 'Helper loaded: '.$helper);
				continue;
			}
			foreach ($this->_ci_helper_paths as $path)
			{
				if (file_exists($path.'services/'.$helper.'.php'))
				{
					include_once($path.'services/'.$helper.'.php');
					$this->_ci_helpers[$helper] = TRUE;
					log_message('info', 'Helper loaded: '.$helper);
					break;
				}
			}
			if ( ! isset($this->_ci_helpers[$helper]))
			{
				show_error('Unable to load the requested file: services/'.$helper.'.php');
			}
		}
		return $this;
	}
	public function helpers($helpers = [])
	{
		return $this->helper($helpers);
	}
	public function language($files, $lang = '')
	{
		get_instance()->lang->load($files, $lang);
		return $this;
	}
	public function config($file, $use_sections = FALSE, $fail_gracefully = FALSE)
	{
		return get_instance()->config->load($file, $use_sections, $fail_gracefully);
	}
	public function driver($library, $params = NULL, $object_name = NULL)
	{
		if (is_array($library))
		{
			foreach ($library as $key => $value)
			{
				if (is_int($key))
				{
					$this->driver($value, $params);
				}
				else
				{
					$this->driver($key, $params, $value);
				}
			}
			return $this;
		}
		elseif (empty($library))
		{
			return FALSE;
		}
		if ( ! class_exists('Driver_Library', FALSE))
		{
			require ADMIN_ROOT.'libraries/Driver.php';
		}
		if ( ! strpos($library, '/'))
		{
			$library = ucfirst($library).'/'.$library;
		}
		return $this->library($library, $params, $object_name);
	}
	public function add_package_path($path, $view_cascade = TRUE)
	{
		$path = rtrim($path, '/').'/';
		array_unshift($this->_ci_library_paths, $path);
		array_unshift($this->_ci_model_paths, $path);
		array_unshift($this->_ci_helper_paths, $path);
		$this->_ci_view_paths = [$path.'views/' => $view_cascade] + $this->_ci_view_paths;
		$config =& $this->_ci_get_component('config');
		$config->_config_paths[] = $path;
		return $this;
	}
	public function get_package_paths($include_base = FALSE)
	{
		return ($include_base === TRUE) ? $this->_ci_library_paths : $this->_ci_model_paths;
	}
	public function remove_package_path($path = '')
	{
		$config =& $this->_ci_get_component('config');
		if ($path === '')
		{
			array_shift($this->_ci_library_paths);
			array_shift($this->_ci_model_paths);
			array_shift($this->_ci_helper_paths);
			array_shift($this->_ci_view_paths);
			array_pop($config->_config_paths);
		}
		else
		{
			$path = rtrim($path, '/').'/';
			foreach (['_ci_library_paths', '_ci_model_paths', '_ci_helper_paths'] as $var)
			{
				if (($key = array_search($path, $this->{$var})) !== FALSE)
				{
					unset($this->{$var}[$key]);
				}
			}
			if (isset($this->_ci_view_paths[$path.'views/']))
			{
				unset($this->_ci_view_paths[$path.'views/']);
			}
			if (($key = array_search($path, $config->_config_paths)) !== FALSE)
			{
				unset($config->_config_paths[$key]);
			}
		}
		$this->_ci_library_paths = array_unique(array_merge($this->_ci_library_paths, [ADMIN_ROOT, ADMIN_ROOT]));
		$this->_ci_helper_paths = array_unique(array_merge($this->_ci_helper_paths, [ADMIN_ROOT, ADMIN_ROOT]));
		$this->_ci_model_paths = array_unique(array_merge($this->_ci_model_paths, [ADMIN_ROOT]));
		$this->_ci_view_paths = array_merge($this->_ci_view_paths, [ADMIN_ROOT.'views/' => TRUE]);
		$config->_config_paths = array_unique(array_merge($config->_config_paths, [ADMIN_ROOT]));
		return $this;
	}
	protected function _ci_load($_ci_data)
	{
		foreach (['_ci_view', '_ci_vars', '_ci_path', '_ci_return'] as $_ci_val)
		{
			$$_ci_val = isset($_ci_data[$_ci_val]) ? $_ci_data[$_ci_val] : FALSE;
		}
		$file_exists = FALSE;
		if (is_string($_ci_path) && $_ci_path !== '')
		{
			$_ci_x = explode('/', $_ci_path);
			$_ci_file = end($_ci_x);
		}
		else
		{
			$_ci_ext = pathinfo($_ci_view, PATHINFO_EXTENSION);
			$_ci_file = ($_ci_ext === '') ? $_ci_view.'.php' : $_ci_view;
			foreach ($this->_ci_view_paths as $_ci_view_file => $cascade)
			{
				if (file_exists($_ci_view_file.$_ci_file))
				{
					$_ci_path = $_ci_view_file.$_ci_file;
					$file_exists = TRUE;
					break;
				}
				if ( ! $cascade)
				{
					break;
				}
			}
		}
		if ( ! $file_exists && ! file_exists($_ci_path))
		{
			show_error('Unable to load the requested file: '.$_ci_file);
		}
		$_ci_CI =& get_instance();
		foreach (get_object_vars($_ci_CI) as $_ci_key => $_ci_var)
		{
			if ( ! isset($this->$_ci_key))
			{
				$this->$_ci_key =& $_ci_CI->$_ci_key;
			}
		}
		empty($_ci_vars) OR $this->_ci_cached_vars = array_merge($this->_ci_cached_vars, $_ci_vars);
		extract($this->_ci_cached_vars);
		ob_start();
		if ( ! is_php('5.4') && ! ini_get('short_open_tag') && config_item('rewrite_short_tags') === TRUE)
		{
			echo eval('?>'.preg_replace('/;*\s*\?>/', '; ?>', str_replace('<?=', '<?php echo ', file_get_contents($_ci_path))));
		}
		else
		{
			include($_ci_path); // include() vs include_once() allows for multiple views with the same name
		}
		log_message('info', 'File loaded: '.$_ci_path);
		if ($_ci_return === TRUE)
		{
			$buffer = ob_get_contents();
			@ob_end_clean();
			return $buffer;
		}
		if (ob_get_level() > $this->_ci_ob_level + 1)
		{
			ob_end_flush();
		}
		else
		{
			$_ci_CI->output->append_output(ob_get_contents());
			@ob_end_clean();
		}
		return $this;
	}
	protected function _ci_load_library($class, $params = NULL, $object_name = NULL)
	{
		$class = str_replace('.php', '', trim($class, '/'));
		if (($last_slash = strrpos($class, '/')) !== FALSE)
		{
			$subdir = substr($class, 0, ++$last_slash);
			$class = substr($class, $last_slash);
		}
		else
		{
			$subdir = '';
		}
		$class = ucfirst($class);
		if (file_exists(ADMIN_ROOT.'services/'.$subdir.$class.'.php'))
		{
			return $this->_ci_load_stock_library($class, $subdir, $params, $object_name);
		}
		if (class_exists($class, FALSE))
		{
			$property = $object_name;
			if (empty($property))
			{
				$property = strtolower($class);
				isset($this->_ci_varmap[$property]) && $property = $this->_ci_varmap[$property];
			}
			$CI =& get_instance();
			if (isset($CI->$property))
			{
				log_message('debug', $class.' class already loaded. Second attempt ignored.');
				return;
			}
			return $this->_ci_init_library($class, '', $params, $object_name);
		}
		foreach ($this->_ci_library_paths as $path)
		{
			if ($path === ADMIN_ROOT)
			{
				continue;
			}
			$filepath = $path.'services/'.$subdir.$class.'.php';
			if ( ! file_exists($filepath))
			{
				continue;
			}
			include_once($filepath);
			return $this->_ci_init_library($class, '', $params, $object_name);
		}
		if ($subdir === '')
		{
			return $this->_ci_load_library($class.'/'.$class, $params, $object_name);
		}
		log_message('error', 'Unable to load the requested class: '.$class);
		show_error('Unable to load the requested class: '.$class);
	}
	protected function _ci_load_stock_library($library_name, $file_path, $params, $object_name)
	{
		$prefix = '';
		if (class_exists($prefix.$library_name, FALSE))
		{
			if (class_exists(config_item('subclass_prefix').$library_name, FALSE))
			{
				$prefix = config_item('subclass_prefix');
			}
			$property = $object_name;
			if (empty($property))
			{
				$property = strtolower($library_name);
				isset($this->_ci_varmap[$property]) && $property = $this->_ci_varmap[$property];
			}
			$CI =& get_instance();
			if ( ! isset($CI->$property))
			{
				return $this->_ci_init_library($library_name, $prefix, $params, $object_name);
			}
			log_message('debug', $library_name.' class already loaded. Second attempt ignored.');
			return;
		}
		$paths = $this->_ci_library_paths;
		array_pop($paths); // ADMIN_ROOT
		array_pop($paths); // ADMIN_ROOT (needs to be the first path checked)
		array_unshift($paths, ADMIN_ROOT);
		foreach ($paths as $path)
		{
			if (file_exists($path = $path.'services/'.$file_path.$library_name.'.php'))
			{
				include_once($path);
				if (class_exists($prefix.$library_name, FALSE))
				{
					return $this->_ci_init_library($library_name, $prefix, $params, $object_name);
				}
				log_message('debug', $path.' exists, but does not declare '.$prefix.$library_name);
			}
		}
		include_once(ADMIN_ROOT.'services/'.$file_path.$library_name.'.php');
		$subclass = config_item('subclass_prefix').$library_name;
		foreach ($paths as $path)
		{
			if (file_exists($path = $path.'services/'.$file_path.$subclass.'.php'))
			{
				include_once($path);
				if (class_exists($subclass, FALSE))
				{
					$prefix = config_item('subclass_prefix');
					break;
				}
				log_message('debug', $path.' exists, but does not declare '.$subclass);
			}
		}
		return $this->_ci_init_library($library_name, $prefix, $params, $object_name);
	}
	protected function _ci_init_library($class, $prefix, $config = FALSE, $object_name = NULL)
	{
		if ($config === NULL)
		{
			$config_component = $this->_ci_get_component('config');
			if (is_array($config_component->_config_paths))
			{
				$found = FALSE;
				foreach ($config_component->_config_paths as $path)
				{
					if (($path === ADMIN_ROOT) ? file_exists(ROOT_DIR . '/config/admin/'.strtolower($class).'.php') : file_exists($path.'config/'.strtolower($class).'.php'))
					{
						include(($path === ADMIN_ROOT) ? ROOT_DIR . '/config/admin/'.strtolower($class).'.php' : $path.'config/'.strtolower($class).'.php');
						$found = TRUE;
					}
					elseif (($path === ADMIN_ROOT) ? file_exists(ROOT_DIR . '/config/admin/'.ucfirst(strtolower($class)).'.php') : file_exists($path.'config/'.ucfirst(strtolower($class)).'.php'))
					{
						include(($path === ADMIN_ROOT) ? ROOT_DIR . '/config/admin/'.ucfirst(strtolower($class)).'.php' : $path.'config/'.ucfirst(strtolower($class)).'.php');
						$found = TRUE;
					}
					if ($found === TRUE)
					{
						break;
					}
				}
			}
		}
		$class_name = $prefix.$class;
		if ( ! class_exists($class_name, FALSE))
		{
			log_message('error', 'Non-existent class: '.$class_name);
			show_error('Non-existent class: '.$class_name);
		}
		if (empty($object_name))
		{
			$object_name = strtolower($class);
			if (isset($this->_ci_varmap[$object_name]))
			{
				$object_name = $this->_ci_varmap[$object_name];
			}
		}
		$CI =& get_instance();
		if (isset($CI->$object_name))
		{
			if ($CI->$object_name instanceof $class_name)
			{
				log_message('debug', $class_name." has already been instantiated as '".$object_name."'. Second attempt aborted.");
				return;
			}
			show_error("Resource '".$object_name."' already exists and is not a ".$class_name." instance.");
		}
		$this->_ci_classes[$object_name] = $class;
		$CI->$object_name = isset($config)
			? new $class_name($config)
			: new $class_name();
	}
	protected function _ci_autoloader()
	{
		if (file_exists(ROOT_DIR . '/config/admin/'.'autoload.php'))
		{
			include(ROOT_DIR . '/config/admin/'.'autoload.php');
		}
		if ( ! isset($autoload))
		{
			return;
		}
		if (isset($autoload['packages']))
		{
			foreach ($autoload['packages'] as $package_path)
			{
				$this->add_package_path($package_path);
			}
		}
		if (count($autoload['config']) > 0)
		{
			foreach ($autoload['config'] as $val)
			{
				$this->config($val);
			}
		}
		foreach (['helper', 'language'] as $type)
		{
			if (isset($autoload[$type]) && count($autoload[$type]) > 0)
			{
				$this->$type($autoload[$type]);
			}
		}
		if (isset($autoload['drivers']))
		{
			$this->driver($autoload['drivers']);
		}
		if (isset($autoload['libraries']) && count($autoload['libraries']) > 0)
		{
			// Database loading removed
			$autoload['libraries'] = array_diff($autoload['libraries'], ['database']);
			$this->library($autoload['libraries']);
		}
	}
	protected function _ci_prepare_view_vars($vars)
	{
		if ( ! is_array($vars))
		{
			$vars = is_object($vars)
				? get_object_vars($vars)
				: [];
		}
		foreach (array_keys($vars) as $key)
		{
			if (strncmp($key, '_ci_', 4) === 0)
			{
				unset($vars[$key]);
			}
		}
		return $vars;
	}
	protected function &_ci_get_component($component)
	{
		$CI =& get_instance();
		return $CI->$component;
	}
}
