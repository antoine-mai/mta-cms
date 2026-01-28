<?php namespace Admin\Core;

/**
 * Registry class to manage singleton instances of core classes/services
 */
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
     * @param string $directory Legacy directory hint (deprecated but supported for compatibility)
     * @param mixed $param Optional parameters for constructor
     * @return object
     */
    public static function &getInstance($class, $directory = 'libraries', $param = NULL)
    {
        // Normalize class name for cache key
        $clean_class = str_replace(['Admin\\Core\\', 'Admin\\Services\\'], '', $class);
        $key = strtolower($clean_class);

        if (isset(self::$_instances[$key])) {
            return self::$_instances[$key];
        }

        // Resolve fully qualified class name
        $fqcn = FALSE;

        // 1. Try direct namespaced class existance (if passed as FQCN)
        if (class_exists($class)) {
            $fqcn = $class;
        } 
        // 2. Try Core namespace
        elseif (class_exists('Admin\\Core\\' . $clean_class)) {
            $fqcn = 'Admin\\Core\\' . $clean_class;
        }
        // 3. Try Services namespace
        elseif (class_exists('Admin\\Services\\' . $clean_class)) {
            $fqcn = 'Admin\\Services\\' . $clean_class;
        }

        if ($fqcn === FALSE) {
            // Legacy fallback using load_class logic from Common (simplified)
             \Admin\Core\Error::show_error("Unable to locate the specified class: {$class}");
        }

        self::$_instances[$key] = isset($param)
            ? new $fqcn($param)
            : new $fqcn();

        // Keep track of loaded classes order if needed, or simply return

        
        return self::$_instances[$key];
    }

    /**
     * Set a specific instance manually (useful for testing or overriding)
     */
    public static function setInstance($key, $instance)
    {
        $key = strtolower(str_replace(['Admin\\Core\\', 'Admin\\Services\\'], '', $key));
        self::$_instances[$key] = $instance;
    }
}
