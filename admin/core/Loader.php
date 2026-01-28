<?php namespace Admin\Core;
/**
 * 
**/
#[\AllowDynamicProperties]
class Loader
{
	protected $ob_level;
	protected $view_paths =	[ADMIN_ROOT . 'template/'	=> TRUE];
	protected $library_paths =	[ADMIN_ROOT, ADMIN_ROOT];
	protected $cached_vars =	[];
	protected $classes =	[];
	protected $varmap =	[
		'unit_test' => 'unit',
		'agent' => 'agent',
		'form_validation' => 'form'
	];
	public function __construct()
	{
		$this->ob_level = ob_get_level();
		// $this->classes check removed as Common::is_loaded is deprecated
		Error::log_message('info', 'Loader Class Initialized');
	}

    /**
     * @var Template
     */
    protected $template;

    protected function getTemplate()
    {
        if (!$this->template) {
            $this->template = new Template();
        }
        return $this->template;
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
		$this->autoloader();
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
		$this->load_library($library, $params, $object_name);
		return $this;
	}
	public function template($view, $vars = [], $return = FALSE)
	{
		return $this->getTemplate()->load($view, $vars, $return);
	}
	public function file($path, $return = FALSE)
	{
		return $this->load(['path' => $path, 'return' => $return]);
	}
	public function vars($vars, $val = '')
	{
		$vars = is_string($vars)
			? [$vars => $val]
			: $this->prepare_view_vars($vars);
		foreach ($vars as $key => $val)
		{
			$this->cached_vars[$key] = $val;
		}
		return $this;
	}
	public function clear_vars()
	{
		$this->cached_vars = [];
		return $this;
	}
	public function get_var($key)
	{
		return isset($this->cached_vars[$key]) ? $this->cached_vars[$key] : NULL;
	}
	public function get_vars()
	{
		return $this->cached_vars;
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

		if ( ! strpos($library, '/'))
		{
			$library = ucfirst($library).'/'.$library;
		}
		return $this->library($library, $params, $object_name);
	}

	protected function load($data)
	{
        // Fallback for file/view loading called internally or via file()
        // Ideally this should also be refactored or delegated
        
		foreach (['view', 'vars', 'path', 'return'] as $val)
		{
			$$val = isset($data[$val]) ? $data[$val] : FALSE;
		}
        
        if ($view) {
             return $this->template($view, $vars, $return);
        }

		$file_exists = FALSE;
		if (is_string($path) && $path !== '')
		{
			$x = explode('/', $path);
			$file = end($x);
		}
        
		if ( ! $file_exists && ! file_exists($path))
		{
			Error::show_error('Unable to load the requested file: '.$file);
		}
        
        // Include logic common for non-template files
		include_once($path); 

		Error::log_message('info', 'File loaded: '.$path);
		return $this;
	}
	protected function load_library($class, $params = NULL, $object_name = NULL)
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
		$psr4_class = 'Admin\\Services\\' . ($subdir ? str_replace('/', '\\', $subdir) : '') . $class;
		if (class_exists($psr4_class))
		{
			return $this->init_library($psr4_class, '', $params, $object_name);
		}
		if (file_exists(ADMIN_ROOT.'services/'.$subdir.$class.'.php'))
		{
			return $this->load_stock_library($class, $subdir, $params, $object_name);
		}
		if (class_exists($class, FALSE))
		{
			$property = $object_name;
			if (empty($property))
			{
				$property = strtolower($class);
				isset($this->varmap[$property]) && $property = $this->varmap[$property];
			}
			$CI =& get_instance();
			if (isset($CI->$property))
			{
				Error::log_message('debug', $class.' class already loaded. Second attempt ignored.');
				return;
			}
			return $this->init_library($class, '', $params, $object_name);
		}
		foreach ($this->library_paths as $path)
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
			return $this->init_library($class, '', $params, $object_name);
		}
		if ($subdir === '')
		{
			return $this->load_library($class.'/'.$class, $params, $object_name);
		}
		Error::log_message('error', 'Unable to load the requested class: '.$class);
		Error::show_error('Unable to load the requested class: '.$class);
	}
	protected function load_stock_library($library_name, $file_path, $params, $object_name)
	{
		$prefix = '';
		if (class_exists($prefix.$library_name, FALSE))
		{
			if (class_exists(Common::config_item('subclass_prefix').$library_name, FALSE))
			{
				$prefix = Common::config_item('subclass_prefix');
			}
			$property = $object_name;
			if (empty($property))
			{
				$property = strtolower($library_name);
				isset($this->varmap[$property]) && $property = $this->varmap[$property];
			}
			$CI =& get_instance();
			if ( ! isset($CI->$property))
			{
				return $this->init_library($library_name, $prefix, $params, $object_name);
			}
			Error::log_message('debug', $library_name.' class already loaded. Second attempt ignored.');
			return;
		}
		$paths = $this->library_paths;
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
					return $this->init_library($library_name, $prefix, $params, $object_name);
				}
				Error::log_message('debug', $path.' exists, but does not declare '.$prefix.$library_name);
			}
		}
		include_once(ADMIN_ROOT.'services/'.$file_path.$library_name.'.php');
		$subclass = Common::config_item('subclass_prefix').$library_name;
		foreach ($paths as $path)
		{
			if (file_exists($path = $path.'services/'.$file_path.$subclass.'.php'))
			{
				include_once($path);
				if (class_exists($subclass, FALSE))
				{
					$prefix = Common::config_item('subclass_prefix');
					break;
				}
				Error::log_message('debug', $path.' exists, but does not declare '.$subclass);
			}
		}
		return $this->init_library($library_name, $prefix, $params, $object_name);
	}
	protected function init_library($class, $prefix, $config = FALSE, $object_name = NULL)
	{
		if ($config === NULL)
		{
			$config_component = $this->get_component('config');
			if (is_array($config_component->_config_paths))
			{
				$found = FALSE;
				foreach ($config_component->_config_paths as $path)
				{
                    $config_file = ($path === ADMIN_ROOT) ? ROOT_DIR . '/config/admin/'.strtolower($class).'.yaml' : $path.'config/'.strtolower($class).'.yaml';
					if (file_exists($config_file))
					{
                        $yaml = new \Admin\Services\Yaml();
						$config = $yaml->parse($config_file);
						$found = TRUE;
					}
					elseif (($path === ADMIN_ROOT) ? file_exists(ROOT_DIR . '/config/admin/'.ucfirst(strtolower($class)).'.yaml') : file_exists($path.'config/'.ucfirst(strtolower($class)).'.yaml'))
					{
                        $config_file = ($path === ADMIN_ROOT) ? ROOT_DIR . '/config/admin/'.ucfirst(strtolower($class)).'.yaml' : $path.'config/'.ucfirst(strtolower($class)).'.yaml';
                        $yaml = new \Admin\Services\Yaml();
						$config = $yaml->parse($config_file);
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
			Error::log_message('error', 'Non-existent class: '.$class_name);
			Error::show_error('Non-existent class: '.$class_name);
		}
		if (empty($object_name))
		{
			$object_name = strtolower($class);
			if (isset($this->varmap[$object_name]))
			{
				$object_name = $this->varmap[$object_name];
			}
		}
		$CI =& get_instance();
		if (isset($CI->$object_name))
		{
			if ($CI->$object_name instanceof $class_name)
			{
				Error::log_message('debug', $class_name." has already been instantiated as '".$object_name."'. Second attempt aborted.");
				return;
			}
			Error::show_error("Resource '".$object_name."' already exists and is not a ".$class_name." instance.");
		}
		$this->classes[$object_name] = $class;
		$CI->$object_name = isset($config)
			? new $class_name($config)
			: new $class_name();
	}
	protected function autoloader()
	{
		if (file_exists(ROOT_DIR . '/config/admin/'.'autoload.yaml'))
		{
            $yaml = new \Admin\Services\Yaml();
			$autoload = $yaml->parse(ROOT_DIR . '/config/admin/'.'autoload.yaml');
		}

		if ( ! isset($autoload))
		{
			return;
		}

		if (count($autoload['config']) > 0)
		{
			foreach ($autoload['config'] as $val)
			{
				$this->config($val);
			}
		}
		foreach (['language'] as $type)
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
	protected function prepare_view_vars($vars)
	{
		if ( ! is_array($vars))
		{
			$vars = is_object($vars)
				? get_object_vars($vars)
				: [];
		}
		foreach (array_keys($vars) as $key)
		{
			if (strncmp($key, 'ci_', 3) === 0)
			{
				unset($vars[$key]);
			}
		}
		return $vars;
	}
	protected function &get_component($component)
	{
		$CI =& get_instance();
		return $CI->$component;
	}
}
