<?php namespace Root\Core;
/**
 * Registry class to manage singleton instances of core classes/services
**/
class Registry
{
    /**
     * @var array
     */
    private static $_instances = [];

    /**
     * Get a singleton instance of a class
     * 
     * @param string $class Class name (short name or namespaced)
     * @param mixed $param Optional parameters for constructor
     * @return object
     */
    public static function &getInstance($class, $param = null)
    {
        // Normalize class name for cache key
        $cleanClass = str_replace(['Root\\Core\\', 'Root\\Services\\'], '', $class);
        $key = strtolower($cleanClass);

        if (isset(self::$_instances[$key])) {
            return self::$_instances[$key];
        }

        // Resolve fully qualified class name
        $fqcn = false;

        // 1. Try direct namespaced class existance (if passed as FQCN)
        if (class_exists($class)) {
            $fqcn = $class;
        } 
        // 2. Try Core namespace
        elseif (class_exists('Root\\Core\\' . $cleanClass)) {
            $fqcn = 'Root\\Core\\' . $cleanClass;
        }
        // 3. Try Services namespace
        elseif (class_exists('Root\\Services\\' . $cleanClass)) {
            $fqcn = 'Root\\Services\\' . $cleanClass;
        }
        // 4. Try Core sub-namespace (Folder/File pattern)
        elseif (class_exists('Root\\Core\\' . $cleanClass . '\\' . $cleanClass)) {
            $fqcn = 'Root\\Core\\' . $cleanClass . '\\' . $cleanClass;
        }
        // 5. Try Services sub-namespace (Folder/File pattern)
        elseif (class_exists('Root\\Services\\' . $cleanClass . '\\' . $cleanClass)) {
            $fqcn = 'Root\\Services\\' . $cleanClass . '\\' . $cleanClass;
        }

        if ($fqcn === false) {
             trigger_error("Unable to locate the specified class: {$class}", E_USER_ERROR);
        }

        // To handle circular dependencies, we instantiate without constructor first,
        // register the instance, and then call the constructor.
        try {
            $reflection = new \ReflectionClass($fqcn);
            $instance = $reflection->newInstanceWithoutConstructor();
            
            // Register early to break cycles
            self::$_instances[$key] = $instance;

            // Call constructor manually
            if ($reflection->hasMethod('__construct')) {
                $constructor = $reflection->getConstructor();
                if ($constructor && $constructor->isPublic()) {
                    if (isset($param)) {
                        $instance->__construct($param);
                    } else {
                        $instance->__construct();
                    }
                }
            }
        } catch (\ReflectionException $e) {
            trigger_error("Reflection error while instantiating {$class}: " . $e->getMessage(), E_USER_ERROR);
        }

        return self::$_instances[$key];
    }

    /**
     * Set a specific instance manually (useful for testing or overriding)
     */
    public static function setInstance($key, $instance)
    {
        $key = strtolower(str_replace(['Root\\Core\\', 'Root\\Services\\'], '', $key));
        self::$_instances[$key] = $instance;
    }
}
