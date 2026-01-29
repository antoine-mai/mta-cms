<?php namespace Admin\Core;
/**
 * Router Class
 *
 * Parses URIs and determines routing.
**/
class Router
{
    /**
     * Config class reference
     *
     * @var Config
     */
    public $config;

    /**
     * List of routes
     *
     * @var array
     */
    public $routes = [];

    /**
     * Current class name
     *
     * @var string
     */
    public $class = '';

    /**
     * Current method name
     *
     * @var string
     */
    public $method = 'index';

    /**
     * Current directory
     *
     * @var string
     */
    public $directory;

    /**
     * Whether to translate URI dashes to underscores
     *
     * @var bool
     */
    public $translateUriDashes = false;

    /**
     * Whether to enable query strings
     *
     * @var bool
     */
    public $enableQueryStrings = false;

    /**
     * Request object reference
     *
     * @var \Admin\Core\Request\Request
     */
    protected $request;

    /**
     * Route parameters
     *
     * @var array
     */
    protected $params = [];

    /**
     * Constructor
     *
     * @param	array	$routing
     */
    public function __construct($routing = null)
    {
        $this->config = &Registry::getInstance('Config');
        $this->enableQueryStrings = (!Console::isCli() && $this->config->item('enable_query_strings') === true);

        if (is_array($routing) && isset($routing['directory'])) {
            $this->setDirectory($routing['directory']);
        }

        $this->setRouting();

        if (is_array($routing)) {
            empty($routing['controller']) or $this->setClass($routing['controller']);
            empty($routing['function']) or $this->setMethod($routing['function']);
        }

        Error::logMessage('info', 'Router Class Initialized');
    }

    /**
     * Set Request object
     *
     * @param	\Admin\Core\Request\Request	$request
     * @return	void
     */
    public function setRequest(\Admin\Core\Request\Request $request)
    {
        $this->request = $request;
    }

    /**
     * Get route parameters
     *
     * @return	array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Set Routing
     *
     * @return	void
     */
    protected function setRouting()
    {
        if (file_exists(CONFPATH . 'routes.yaml')) {
            if (!class_exists('Admin\Services\Yaml')) {
                require_once ADMIN_ROOT . 'services/Yaml.php';
            }
            $yaml = new \Admin\Services\Yaml();
            $route = $yaml->parse(CONFPATH . 'routes.yaml');
        }

        if (isset($route) && is_array($route)) {
            isset($route['translate_uri_dashes']) && $this->translateUriDashes = $route['translate_uri_dashes'];
            unset($route['translate_uri_dashes']);
            $this->routes = $route;
        }

        if ($this->enableQueryStrings) {
            if (!isset($this->directory)) {
                $d = $this->config->item('directory_trigger');
                $d = isset($_GET[$d]) ? trim($_GET[$d], " \t\n\r\0\x0B/") : '';
                if ($d !== '') {
                    $this->setDirectory($d);
                }
            }

            $c = trim((string)$this->config->item('controller_trigger'));
            if (!empty($_GET[$c])) {
                $this->setClass($_GET[$c]);
                $f = trim((string)$this->config->item('function_trigger'));
                if (!empty($_GET[$f])) {
                    $this->setMethod($_GET[$f]);
                }
            }
            return;
        }

        $this->parseRoutes();
    }

    /**
     * Internal request setup
     *
     * @param	array	$segments
     * @return	void
     */
    protected function setupRequest($segments = [])
    {
        $segments = $this->validateRequest($segments);
        if (empty($segments)) {
            return;
        }

        if ($this->translateUriDashes === true) {
            $segments[0] = str_replace('-', '_', $segments[0]);
            if (isset($segments[1])) {
                $segments[1] = str_replace('-', '_', $segments[1]);
            }
        }

        $this->setClass($segments[0]);
        if (isset($segments[1])) {
            $this->setMethod($segments[1]);
        } else {
            $segments[1] = 'index';
        }

        array_unshift($segments, null);
        unset($segments[0]);

        $this->params = array_slice($segments, 2);
    }

    /**
     * Validate Request
     *
     * @param	array	$segments
     * @return	array
     */
    protected function validateRequest($segments)
    {
        $c = count($segments);
        $directoryOverride = isset($this->directory);

        while ($c-- > 0) {
            $test = (string)$this->directory
                . ucfirst($this->translateUriDashes === true ? str_replace('-', '_', $segments[0]) : $segments[0]);

            if (
                !file_exists(ADMIN_ROOT . 'pages/' . $test . '.php')
                && $directoryOverride === false
                && is_dir(ADMIN_ROOT . 'pages/' . (string)$this->directory . $segments[0])
            ) {
                $this->setDirectory(array_shift($segments), true);
                continue;
            }
            return $segments;
        }
        return $segments;
    }

    /**
     * Parse Routes
     *
     * @return	void
     */
    protected function parseRoutes()
    {
        if ($this->request) {
            $uri = $this->request->getPathInfo();
        } else {
            $uri = isset($_SERVER['REQUEST_URI']) ? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : '/';
            $scriptName = $_SERVER['SCRIPT_NAME'];
            if (strpos($uri, $scriptName) === 0) {
                $uri = substr($uri, strlen($scriptName));
            } elseif (strpos($uri, dirname($scriptName)) === 0) {
                $uri = substr($uri, strlen(dirname($scriptName)));
            }
        }

        $uri = ($uri === '' || $uri === null) ? '/' : $uri;
        $httpVerb = isset($_SERVER['REQUEST_METHOD']) ? strtolower($_SERVER['REQUEST_METHOD']) : 'cli';

        foreach ($this->routes as $name => $data) {
            $path = $data;
            $controller = $data;

            if (is_array($data)) {
                if (isset($data['path'])) {
                    $path = $data['path'];
                    $controller = isset($data['controller']) ? $data['controller'] : $data;
                } else {
                    $path = (string)$name;
                    $controller = isset($data[$httpVerb]) ? $data[$httpVerb] : (isset($data['controller']) ? $data['controller'] : $data);
                }
            }

            $routeKey = (string)$path;
            if ($routeKey !== '/' && strpos($routeKey, '/') === 0) {
                $routeKey = substr($routeKey, 1);
            }

            if ($uri === '/' && ($routeKey === '/' || $routeKey === '')) {
                $this->setRouteParams($controller);
                return;
            }

            if ($routeKey === '/') continue;

            $routeKey = str_replace([':any', ':num'], ['[^/]+', '[0-9]+'], $routeKey);
            if (preg_match('#^' . $routeKey . '$#', $uri, $matches)) {
                $this->setRouteParams($controller, $matches, $uri, $routeKey);
                return;
            }
        }

        if ($uri === '/') {
            Error::showError('No route found for /');
        }

        $segments = explode('/', trim((string)$uri, '/'));
        $this->setupRequest($segments);
    }

    /**
     * Set Route Parameters
     */
    protected function setRouteParams($controller, $matches = [], $uri = '', $routeKey = '')
    {
        if (!is_string($controller) && is_callable($controller)) {
            array_shift($matches);
            $controller = call_user_func_array($controller, $matches);
        } elseif (is_string($controller)) {
            $controller = (string)str_replace('::', '/', $controller);

            if (strpos($controller, '$') !== false && strpos($routeKey, '(') !== false) {
                $controller = (string)preg_replace('#^' . $routeKey . '$#', $controller, $uri);
            }
        }

        $this->setupRequest(explode('/', (string)$controller));
    }

    /**
     * Set Class
     */
    public function setClass($class)
    {
        $this->class = str_replace(['/', '.'], '', (string)$class);
    }

    /**
     * Set Method
     */
    public function setMethod($method)
    {
        $this->method = (string)$method;
    }

    /**
     * Set Directory
     */
    public function setDirectory($dir, $append = false)
    {
        if ($append !== true || empty($this->directory)) {
            $this->directory = str_replace('.', '', trim((string)$dir, '/')) . '/';
        } else {
            $this->directory .= str_replace('.', '', trim((string)$dir, '/')) . '/';
        }
    }
}
