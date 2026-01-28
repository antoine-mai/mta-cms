<?php namespace Admin\Core;
/**
 * 
**/
#[\AllowDynamicProperties]
class Router
{
	public $config;
	public $routes =	[];
	public $class =		'';
	public $method =	'index';
	public $directory;
	public $translate_uri_dashes = false;
	public $enable_query_strings = false;
    /**
     * @var \Admin\Core\Request\Request
     */
    protected $request;
    protected $params = [];

	public function __construct($routing = null)
	{
		$this->config =& \Admin\Core\Registry::getInstance('Config', 'core');
        // $this->uri =& \Admin\Core\Registry::getInstance('URI', 'core'); // Deprecated
		$this->enable_query_strings = ( ! Console::isCli() && $this->config->item('enable_query_strings') === true);
		
        if (is_array($routing) && isset($routing['directory'])) {
             $this->setDirectory($routing['directory']);
        }
        
        $this->setRouting();
        
		if (is_array($routing))
		{
			empty($routing['controller']) OR $this->setClass($routing['controller']);
			empty($routing['function'])   OR $this->setMethod($routing['function']);
		}
		Error::logMessage('info', 'Router Class Initialized');
	}
    
    public function setRequest(\Admin\Core\Request\Request $request)
    {
        $this->request = $request;
    }
    
    public function getParams()
    {
        return $this->params;
    }
	protected function setRouting()
	{
		if (file_exists(CONFPATH.'routes.yaml'))
		{
            if (!class_exists('Admin\Services\Yaml')) {
                require_once ADMIN_ROOT . 'services/Yaml.php';
            }
            $yaml = new \Admin\Services\Yaml();
			$route = $yaml->parse(CONFPATH.'routes.yaml');
		}

		if (isset($route) && is_array($route))
		{
			isset($route['translate_uri_dashes']) && $this->translate_uri_dashes = $route['translate_uri_dashes'];
			unset($route['translate_uri_dashes']);
			$this->routes = $route;
		}
		if ($this->enable_query_strings)
		{
			if ( ! isset($this->directory))
			{
				$_d = $this->config->item('directory_trigger');
				$_d = isset($_GET[$_d]) ? trim($_GET[$_d], " \t\n\r\0\x0B/") : '';
				if ($_d !== '')
				{
					$this->uri->filter_uri($_d);
					$this->setDirectory($_d);
				}
			}
			$_c = trim($this->config->item('controller_trigger'));
			if ( ! empty($_GET[$_c]))
			{
				$this->uri->filter_uri($_GET[$_c]);
				$this->setClass($_GET[$_c]);
				$_f = trim($this->config->item('function_trigger'));
				if ( ! empty($_GET[$_f]))
				{
					$this->uri->filter_uri($_GET[$_f]);
					$this->setMethod($_GET[$_f]);
				}
				$this->uri->rsegments = [
					1 => $this->class,
					2 => $this->method
				];
			}
			else
			{
				// $this->_set_default_controller();
			}
			return;
		}
		$this->parseRoutes();
	}
	protected function setRequest($segments = [])
	{
		$segments = $this->validateRequest($segments);
		if (empty($segments))
		{
			return;
		}
		if ($this->translate_uri_dashes === true)
		{
			$segments[0] = str_replace('-', '_', $segments[0]);
			if (isset($segments[1]))
			{
				$segments[1] = str_replace('-', '_', $segments[1]);
			}
		}
		$this->setClass($segments[0]);
		if (isset($segments[1]))
		{
			$this->setMethod($segments[1]);
		}
		else
		{
			$segments[1] = 'index';
		}
		array_unshift($segments, null);
		unset($segments[0]);
		
        // Store params
        $this->params = array_slice($segments, 2);
	}

	protected function validateRequest($segments)
	{
		$c = count($segments);
		$directory_override = isset($this->directory);
		while ($c-- > 0)
		{
			$test = $this->directory
				.ucfirst($this->translate_uri_dashes === true ? str_replace('-', '_', $segments[0]) : $segments[0]);
			if ( ! file_exists(ADMIN_ROOT.'routes/'.$test.'.php')
				&& $directory_override === false
				&& is_dir(ADMIN_ROOT.'routes/'.$this->directory.$segments[0])
			)
			{
				$this->setDirectory(array_shift($segments), true);
				continue;
			}
			return $segments;
		}
		return $segments;
	}
	protected function parseRoutes()
	{
        // Use Request path info if available, otherwise fallback (for now)
        if ($this->request) {
            $uri = $this->request->getPathInfo();
        } else {
            // Fallback to manual parsing if Request not yet set (though it should be)
             $uri = isset($_SERVER['REQUEST_URI']) ? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : '/';
             // Simple cleanup
             if (strpos($uri, $_SERVER['SCRIPT_NAME']) === 0) {
                 $uri = substr($uri, strlen($_SERVER['SCRIPT_NAME']));
             } elseif (strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0) {
                  $uri = substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
             }
        }
        
        $uri = ($uri === '') ? '/' : $uri;

		$http_verb = isset($_SERVER['REQUEST_METHOD']) ? strtolower($_SERVER['REQUEST_METHOD']) : 'cli';
		
        foreach ($this->routes as $name => $data)
		{
            $path = $data;
            $controller = $data;

            if (is_array($data))
            {
                if (isset($data['path']))
                {
                    $path = $data['path'];
                    $controller = isset($data['controller']) ? $data['controller'] : $data;
                }
                else
                {
                    $path = $name;
                    $controller = isset($data[$http_verb]) ? $data[$http_verb] : (isset($data['controller']) ? $data['controller'] : $data);
                }
            }

            if (is_array($controller)) continue; // Should have been handled above

            // Leading slash handling
            $route_key = $path;
            if ($route_key !== '/' && strpos($route_key, '/') === 0)
            {
                $route_key = substr($route_key, 1);
            }

            // Root route check
            if ($uri === '/' && ($route_key === '/' OR $route_key === ''))
            {
                $this->setRouteParams($controller);
                return;
            }

            if ($route_key === '/') continue;

			$route_key = str_replace([':any', ':num'], ['[^/]+', '[0-9]+'], $route_key);
			if (preg_match('#^'.$route_key.'$#', $uri, $matches))
			{
				$this->setRouteParams($controller, $matches, $uri, $route_key);
				return;
			}
		}

        // Fallback for empty URI if no root route matched
        if ($uri === '/')
        {
        // Fallback or default
            Error::showError('No route found for /');
        }

		// $this->setRequest(array_values($this->uri->segments));
        $segments = explode('/', trim($uri, '/'));
        $this->setRequest($segments);
	}

    protected function setRouteParams($controller, $matches = [], $uri = '', $route_key = '')
    {
        if ( ! is_string($controller) && is_callable($controller))
        {
            array_shift($matches);
            $controller = call_user_func_array($controller, $matches);
        }
        elseif (is_string($controller))
        {
            $controller = str_replace('::', '/', $controller);

            if (strpos($controller, '$') !== false && strpos($route_key, '(') !== false)
            {
                $controller = preg_replace('#^'.$route_key.'$#', $controller, $uri);
            }
        }
        
        $this->setRequest(explode('/', $controller));
    }
	public function setClass($class)
	{
		$this->class = str_replace(['/', '.'], '', $class);
	}

	public function setMethod($method)
	{
		$this->method = $method;
	}

	public function setDirectory($dir, $append = false)
	{
		if ($append !== true OR empty($this->directory))
		{
			$this->directory = str_replace('.', '', trim($dir, '/')).'/';
		}
		else
		{
			$this->directory .= str_replace('.', '', trim($dir, '/')).'/';
		}
	}
}
