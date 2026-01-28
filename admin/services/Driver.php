<?php namespace Admin\Services;

use ReflectionObject;
use BadMethodCallException;

class Driver {
	// From DriverLibrary
	protected $valid_drivers = [];
	protected $lib_name;

	// From Driver
	protected $_parent;
	protected $_methods = [];
	protected $_properties = [];
	protected static $_reflections = [];

	/**
	 * Loader behavior (from DriverLibrary)
	 */
	public function __get($child)
	{
		// Try to load driver if it's in valid_drivers
		if (in_array($child, $this->valid_drivers))
		{
			return $this->load_driver($child);
		}

		// Decoration proxy behavior (from Driver)
		if (isset($this->_parent, $this->_properties) && in_array($child, $this->_properties))
		{
			return $this->_parent->$child;
		}

		return NULL;
	}

	public function load_driver($child)
	{
		$prefix = config_item('subclass_prefix');
		if ( ! isset($this->lib_name))
		{
			$this->lib_name = str_replace([$prefix, 'Admin\\Services\\'], '', (string)get_class($this));
		}

		$child_name = $this->lib_name.'_'.$child;
		if ( ! in_array($child, $this->valid_drivers))
		{
			$msg = 'Invalid driver requested: '.$child_name;
			log_message('error', $msg);
			show_error($msg);
		}

		$CI = get_instance();
		$paths = $CI->load->get_package_paths(TRUE);
		
        // Try PSR-4 first
        $namespace = get_class($this);
        $driver_class = $namespace . '\\Drivers\\' . ucfirst((string)$child);
        if (class_exists($driver_class)) {
            $class_name = $driver_class;
        } else {
            $driver_class = $namespace . '\\' . ucfirst((string)$child);
            if (class_exists($driver_class)) {
                $class_name = $driver_class;
            } else {
                // Fallback to legacy loading
                $class_name = $prefix.$child_name;
                $found = class_exists($class_name, FALSE);
                if ( ! $found)
                {
                    foreach ($paths as $path)
                    {
                        $file = $path.'libraries/'.$this->lib_name.'/drivers/'.$prefix.$child_name.'.php';
                        if (file_exists((string)$file))
                        {
                            $basepath = ADMIN_ROOT.'libraries/'.$this->lib_name.'/drivers/'.$child_name.'.php';
                            if ( ! file_exists((string)$basepath))
                            {
                                $msg = 'Unable to load the requested class: CI_'.$child_name;
                                log_message('error', $msg);
                                show_error($msg);
                            }
                            include_once($basepath);
                            include_once($file);
                            $found = TRUE;
                            break;
                        }
                    }
                }

                if ( ! $found)
                {
                    $class_name = ''.$child_name;
                    if ( ! class_exists($class_name, FALSE))
                    {
                        foreach ($paths as $path)
                        {
                            $file = $path.'libraries/'.$this->lib_name.'/drivers/'.$child_name.'.php';
                            if (file_exists((string)$file))
                            {
                                include_once($file);
                                break;
                            }
                        }
                    }
                }

                if ( ! class_exists($class_name, FALSE))
                {
                    if (class_exists($child_name, FALSE))
                    {
                        $class_name = $child_name;
                    }
                    else
                    {
                        $msg = 'Unable to load the requested driver: '.$class_name;
                        log_message('error', $msg);
                        show_error($msg);
                    }
                }
            }
        }

		$obj = new $class_name();
		if (method_exists($obj, 'decorate')) {
            $obj->decorate($this);
        }
		$this->$child = $obj;
		return $this->$child;
	}

	/**
	 * Decoration behavior (from Driver)
	 */
	public function decorate($parent)
	{
		$this->_parent = $parent;
		$class_name = get_class($parent);
		if ( ! isset(self::$_reflections[$class_name]))
		{
			$r = new ReflectionObject($parent);
			foreach ($r->getMethods() as $method)
			{
				if ($method->isPublic())
				{
					$this->_methods[] = $method->getName();
				}
			}
			foreach ($r->getProperties() as $prop)
			{
				if ($prop->isPublic())
				{
					$this->_properties[] = $prop->getName();
				}
			}
			self::$_reflections[$class_name] = [$this->_methods, $this->_properties];
		}
		else
		{
			list($this->_methods, $this->_properties) = self::$_reflections[$class_name];
		}
	}

	public function __call($method, $args = [])
	{
		if (isset($this->_parent, $this->_methods) && in_array($method, $this->_methods))
		{
			return call_user_func_array([$this->_parent, $method], $args);
		}
		throw new BadMethodCallException('No such method: '.$method.'()');
	}

	public function __set($var, $val)
	{
		if (isset($this->_parent, $this->_properties) && in_array($var, $this->_properties))
		{
			$this->_parent->$var = $val;
		}
	}
}
