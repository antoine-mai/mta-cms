<?php namespace Admin\Core;

/**
 * Loader Class
 *
 * Loads framework components.
 */
#[\AllowDynamicProperties]
class Loader
{
    /**
     * Nesting level of the output buffering mechanism
     *
     * @var int
     */
    protected $obLevel;

    /**
     * List of paths to load views from
     *
     * @var array
     */
    protected $viewPaths = [ADMIN_ROOT . 'template/' => true];

    /**
     * List of paths to load libraries from
     *
     * @var array
     */
    protected $libraryPaths = [ADMIN_ROOT, ADMIN_ROOT];

    /**
     * List of cached variables
     *
     * @var array
     */
    protected $cachedVars = [];

    /**
     * List of loaded classes
     *
     * @var array
     */
    protected $classes = [];

    /**
     * Variable name map
     *
     * @var array
     */
    protected $varmap = [
        'unit_test' => 'unit',
        'agent' => 'agent',
        'form_validation' => 'form'
    ];

    /**
     * Template instance
     *
     * @var Template
     */
    protected $template;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->obLevel = ob_get_level();
        Error::logMessage('info', 'Loader Class Initialized');
    }

    /**
     * Get template instance
     *
     * @return Template
     */
    protected function getTemplate()
    {
        if (!$this->template) {
            $this->template = new Template();
        }
        return $this->template;
    }

    /**
     * Autoload PSR-4 namespaces
     *
     * @param	array	$namespaces
     * @return	void
     */
    public function autoloadPsr4(array $namespaces = [])
    {
        foreach ($namespaces as $prefix => $baseDir) {
            $prefix = trim($prefix, '\\') . '\\';
            $baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR) . '/';

            spl_autoload_register(function ($class) use ($prefix, $baseDir) {
                $len = strlen($prefix);
                if (strncmp($prefix, $class, $len) !== 0) {
                    return;
                }

                $relativeClass = substr($class, $len);
                $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

                if (file_exists($file)) {
                    require $file;
                }
            });
        }
    }

    /**
     * Initialize loader
     */
    public function initialize()
    {
        $this->autoloader();
    }

    /**
     * Load library
     *
     * @param	mixed	$library	Library name
     * @param	array	$params		Optional parameters
     * @param	string	$objectName	Optional object name
     * @return	object
     */
    public function library($library, $params = null, $objectName = null)
    {
        if (empty($library)) {
            return $this;
        } elseif (is_array($library)) {
            foreach ($library as $key => $value) {
                if (is_int($key)) {
                    $this->library($value, $params);
                } else {
                    $this->library($key, $params, $value);
                }
            }
            return $this;
        }

        if ($params !== null && !is_array($params)) {
            $params = null;
        }

        $this->loadLibrary($library, $params, $objectName);
        return $this;
    }

    /**
     * Load template
     */
    public function template($view, $vars = [], $return = false)
    {
        return $this->getTemplate()->load($view, $vars, $return);
    }

    /**
     * Load file
     */
    public function file($path, $return = false)
    {
        return $this->load(['path' => $path, 'return' => $return]);
    }

    /**
     * Set variables for views
     */
    public function vars($vars, $val = '')
    {
        $vars = is_string($vars)
            ? [$vars => $val]
            : $this->prepareViewVars($vars);

        foreach ($vars as $key => $val) {
            $this->cachedVars[$key] = $val;
        }

        return $this;
    }

    /**
     * Clear view variables
     */
    public function clearVars()
    {
        $this->cachedVars = [];
        return $this;
    }

    /**
     * Get a view variable
     */
    public function getVar($key)
    {
        return isset($this->cachedVars[$key]) ? $this->cachedVars[$key] : null;
    }

    /**
     * Get all view variables
     */
    public function getVars()
    {
        return $this->cachedVars;
    }

    /**
     * Load language
     */
    public function language($files, $lang = '')
    {
        getInstance()->lang->load($files, $lang);
        return $this;
    }

    /**
     * Load config
     */
    public function config($file, $useSections = false, $failGracefully = false)
    {
        return getInstance()->config->load($file, $useSections, $failGracefully);
    }

    /**
     * Load driver
     */
    public function driver($library, $params = null, $objectName = null)
    {
        if (is_array($library)) {
            foreach ($library as $key => $value) {
                if (is_int($key)) {
                    $this->driver($value, $params);
                } else {
                    $this->driver($key, $params, $value);
                }
            }
            return $this;
        } elseif (empty($library)) {
            return false;
        }

        if (!strpos($library, '/')) {
            $library = ucfirst($library) . '/' . $library;
        }

        return $this->library($library, $params, $objectName);
    }

    /**
     * Internal load method
     */
    protected function load($data)
    {
        foreach (['view', 'vars', 'path', 'return'] as $val) {
            $$val = isset($data[$val]) ? $data[$val] : false;
        }

        if ($view) {
            return $this->template($view, $vars, $return);
        }

        if (is_string($path) && $path !== '') {
            $x = explode('/', $path);
            $file = end($x);
        }

        if (!file_exists($path)) {
            Error::showError('Unable to load the requested file: ' . $file);
        }

        include_once($path);
        Error::logMessage('info', 'File loaded: ' . $path);

        return $this;
    }

    /**
     * Load library internal
     */
    protected function loadLibrary($class, $params = null, $objectName = null)
    {
        $class = str_replace('.php', '', trim($class, '/'));

        if (($lastSlash = strrpos($class, '/')) !== false) {
            $subdir = substr($class, 0, ++$lastSlash);
            $class = substr($class, $lastSlash);
        } else {
            $subdir = '';
        }

        $class = ucfirst($class);
        $psr4Class = 'Admin\\Services\\' . ($subdir ? str_replace('/', '\\', $subdir) : '') . $class;

        if (class_exists($psr4Class)) {
            return $this->initLibrary($psr4Class, '', $params, $objectName);
        }

        if (file_exists(ADMIN_ROOT . 'services/' . $subdir . $class . '.php')) {
            return $this->loadStockLibrary($class, $subdir, $params, $objectName);
        }

        if (class_exists($class, false)) {
            $property = $objectName;
            if (empty($property)) {
                $property = strtolower($class);
                isset($this->varmap[$property]) && $property = $this->varmap[$property];
            }

            $CI = &getInstance();
            if (isset($CI->$property)) {
                Error::logMessage('debug', $class . ' class already loaded. Second attempt ignored.');
                return;
            }
            return $this->initLibrary($class, '', $params, $objectName);
        }

        foreach ($this->libraryPaths as $path) {
            if ($path === ADMIN_ROOT) {
                continue;
            }
            $filepath = $path . 'services/' . $subdir . $class . '.php';
            if (!file_exists($filepath)) {
                continue;
            }
            include_once($filepath);
            return $this->initLibrary($class, '', $params, $objectName);
        }

        if ($subdir === '') {
            return $this->loadLibrary($class . '/' . $class, $params, $objectName);
        }

        Error::logMessage('error', 'Unable to load the requested class: ' . $class);
        Error::showError('Unable to load the requested class: ' . $class);
    }

    /**
     * Load stock library
     */
    protected function loadStockLibrary($libraryName, $filePath, $params, $objectName)
    {
        $prefix = '';
        if (class_exists($prefix . $libraryName, false)) {
            if (class_exists(Common::configItem('subclass_prefix') . $libraryName, false)) {
                $prefix = Common::configItem('subclass_prefix');
            }

            $property = $objectName;
            if (empty($property)) {
                $property = strtolower($libraryName);
                isset($this->varmap[$property]) && $property = $this->varmap[$property];
            }

            $CI = &getInstance();
            if (!isset($CI->$property)) {
                return $this->initLibrary($libraryName, $prefix, $params, $objectName);
            }

            Error::logMessage('debug', $libraryName . ' class already loaded. Second attempt ignored.');
            return;
        }

        $paths = $this->libraryPaths;
        array_pop($paths); // ADMIN_ROOT
        array_pop($paths); // ADMIN_ROOT
        array_unshift($paths, ADMIN_ROOT);

        foreach ($paths as $path) {
            if (file_exists($path = $path . 'services/' . $filePath . $libraryName . '.php')) {
                include_once($path);
                if (class_exists($prefix . $libraryName, false)) {
                    return $this->initLibrary($libraryName, $prefix, $params, $objectName);
                }
                Error::logMessage('debug', $path . ' exists, but does not declare ' . $prefix . $libraryName);
            }
        }

        include_once(ADMIN_ROOT . 'services/' . $filePath . $libraryName . '.php');
        $subclass = Common::configItem('subclass_prefix') . $libraryName;

        foreach ($paths as $path) {
            if (file_exists($path = $path . 'services/' . $filePath . $subclass . '.php')) {
                include_once($path);
                if (class_exists($subclass, false)) {
                    $prefix = Common::configItem('subclass_prefix');
                    break;
                }
                Error::logMessage('debug', $path . ' exists, but does not declare ' . $subclass);
            }
        }

        return $this->initLibrary($libraryName, $prefix, $params, $objectName);
    }

    /**
     * Initialize library
     */
    protected function initLibrary($class, $prefix, $config = false, $objectName = null)
    {
        if ($config === null) {
            $configFile = ROOT_DIR . '/config/admin/' . strtolower($class) . '.yaml';
            if (file_exists($configFile)) {
                $yaml = new \Admin\Services\Yaml();
                $config = $yaml->parse($configFile);
            } elseif (file_exists($configFile = ROOT_DIR . '/config/admin/' . ucfirst(strtolower($class)) . '.yaml')) {
                $yaml = new \Admin\Services\Yaml();
                $config = $yaml->parse($configFile);
            }
        }

        $className = $prefix . $class;
        if (!class_exists($className, false)) {
            Error::logMessage('error', 'Non-existent class: ' . $className);
            Error::showError('Non-existent class: ' . $className);
        }

        if (empty($objectName)) {
            $objectName = strtolower($class);
            if (isset($this->varmap[$objectName])) {
                $objectName = $this->varmap[$objectName];
            }
        }

        $CI = &getInstance();
        if (isset($CI->$objectName)) {
            if ($CI->$objectName instanceof $className) {
                Error::logMessage('debug', $className . " has already been instantiated as '" . $objectName . "'. Second attempt aborted.");
                return;
            }
            Error::showError("Resource '" . $objectName . "' already exists and is not a " . $className . " instance.");
        }

        $this->classes[$objectName] = $class;
        $CI->$objectName = isset($config)
            ? new $className($config)
            : new $className();
    }

    /**
     * Autoloader
     */
    protected function autoloader()
    {
        if (file_exists(ROOT_DIR . '/config/admin/autoload.yaml')) {
            $yaml = new \Admin\Services\Yaml();
            $autoload = $yaml->parse(ROOT_DIR . '/config/admin/autoload.yaml');
        }

        if (!isset($autoload)) {
            return;
        }

        if (isset($autoload['config']) && count($autoload['config']) > 0) {
            foreach ($autoload['config'] as $val) {
                $this->config($val);
            }
        }

        foreach (['language'] as $type) {
            if (isset($autoload[$type]) && count($autoload[$type]) > 0) {
                $this->$type($autoload[$type]);
            }
        }

        if (isset($autoload['drivers'])) {
            $this->driver($autoload['drivers']);
        }

        if (isset($autoload['libraries']) && count($autoload['libraries']) > 0) {
            $autoload['libraries'] = array_diff($autoload['libraries'], ['database']);
            $this->library($autoload['libraries']);
        }
    }

    /**
     * Prepare view variables
     */
    protected function prepareViewVars($vars)
    {
        if (!is_array($vars)) {
            $vars = is_object($vars) ? get_object_vars($vars) : [];
        }

        foreach (array_keys($vars) as $key) {
            if (strncmp($key, 'ci_', 3) === 0) {
                unset($vars[$key]);
            }
        }

        return $vars;
    }

    /**
     * Get component from global instance
     */
    protected function &getComponent($component)
    {
        $CI = &getInstance();
        return $CI->$component;
    }
}
