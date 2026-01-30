<?php namespace Root\Core;
/**
 * Loader Class
 *
 * Responsible for loading core, services, routes, and managing templates.
**/
class Loader
{
    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Autoload
     *
     * Registers a spl_autoload_register for Root namespaces and immediately
     * loads all files in core, services, and routes.
     *
     * @return void
     */
    public static function autoload()
    {
        // Register autoloader first as a fallback/helper
        spl_autoload_register([self::class, 'handleAutoload'], true, false);

        // Immediately load all files in core, services, and routes
        $baseDir = dirname(__DIR__);
        $dirs = [
            $baseDir . '/services',
            $baseDir . '/pages',
            $baseDir . '/core'
        ];

        foreach ($dirs as $dir) {
            self::loadDirRecursive($dir);
        }
    }

    /**
     * Recursively load all PHP files in a directory
     *
     * @param string $dir
     * @return void
     */
    protected static function loadDirRecursive($dir)
    {
        if (!is_dir((string)$dir)) {
            return;
        }

        $items = scandir((string)$dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                self::loadDirRecursive($path);
            } elseif (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
                require_once($path);
            }
        }
    }

    /**
     * Handle Autoload
     *
     * @param string $class
     * @return void
     */
    public static function handleAutoload($class)
    {
        $class = ltrim((string)$class, '\\');
        if (strpos($class, 'Root\\') !== 0) {
            return;
        }

        $baseDir = dirname(__DIR__);
        $relativeClass = substr($class, 5); // Remove 'Root\'

        // 1. Try exact case mapping
        $path = $baseDir . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';
        if (file_exists($path)) {
            require_once($path);
            return;
        }

        // 2. Try segmented approach (handling mixed case directories like 'core/Response')
        $parts = explode('\\', $relativeClass);
        $fileName = array_pop($parts) . '.php';
        
        if (!empty($parts)) {
            // Most core/service dirs are lowercase
            $parts[0] = strtolower($parts[0]);
            $path = $baseDir . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $parts) . DIRECTORY_SEPARATOR . $fileName;
            if (file_exists($path)) {
                require_once($path);
                return;
            }
        }

        // 3. Try lowercase for all directory parts
        $parts = explode('\\', $relativeClass);
        $fileName = array_pop($parts) . '.php';
        $subDir = strtolower(implode(DIRECTORY_SEPARATOR, $parts));
        $path = $baseDir . DIRECTORY_SEPARATOR . $subDir . DIRECTORY_SEPARATOR . $fileName;
        if (file_exists($path)) {
            require_once($path);
            return;
        }
    }

    /**
     * Load language file
     * 
     * @param string|array $file
     * @param string $idiom
     * @param bool $return
     * @return mixed
     */
    public function language($file, $idiom = '', $return = false)
    {
        $lang = &Registry::getInstance('Language');
        return $lang->load($file, $idiom, $return);
    }

}
