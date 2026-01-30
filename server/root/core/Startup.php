<?php namespace Root\Core;
/**
 * 
**/
use \Root\Core\Response\Response;
use \Root\Core\Request\Request;
/**
 * Startup Class
 *
 * Orchestrates the application flow.
**/
class Startup
{
    /**
     * Run the application
     */
    public static function run()
    {
        // 1. Load Config early to get paths
        $config = Registry::getInstance('Config');
        $rootDir = $config->getRootDir();

        // 2. Load Environment Variables
        if (file_exists($rootDir . '/.env')) {
            $lines = file($rootDir . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                $parts = explode('=', $line, 2);
                if (count($parts) === 2) {
                    $name = trim($parts[0]);
                    $value = trim($parts[1]);
                    // Remove quotes if present
                    if (preg_match('/^"(.*)"$/', $value, $matches) || preg_match("/^'(.*)'$/", $value, $matches)) {
                        $value = $matches[1];
                    }
                    if (!isset($_SERVER[$name]) && !isset($_ENV[$name])) {
                        putenv(sprintf('%s=%s', $name, $value));
                        $_ENV[$name] = $value;
                        $_SERVER[$name] = $value;
                    }
                }
            }
        }

        // Validate Critical Configuration
        $rootUser = $_ENV['ROOT_USER'] ?? null;
        $rootPass = $_ENV['ROOT_PASS'] ?? null;

        if (empty($rootUser) || empty($rootPass)) {
            // Check if request is for API to return JSON error
            if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/post/') !== false) {
                 header('Content-Type: application/json');
                 http_response_code(503);
                 echo json_encode(['error' => 'Server Configuration Error: Root credentials not set.']);
                 exit;
            }
            
            // Otherwise display HTML error
            header('HTTP/1.1 503 Service Unavailable');
            echo '<html><body style="font-family:sans-serif; text-align:center; padding-top:50px;">';
            echo '<h1 style="color:#d9534f;">Configuration Error</h1>';
            echo '<p>The application is missing required configuration.</p>';
            echo '<p>Please ensure <code>.env</code> file exists and contains <code>ROOT_USER</code> and <code>ROOT_PASS</code>.</p>';
            echo '</body></html>';
            exit;
        }

        // 3. Setup Environment
        self::init();

        // 4. Create Request
        $request = Request::createFromGlobals();
        Registry::setInstance('Request', $request);
        
        // Debug log (can be removed later)
        // error_log("Request: " . $request->server->get('REQUEST_URI') . " -> " . $request->getPathInfo());
         // 5. Load Core Components
        Registry::getInstance('Utf8');
        $URI = &Registry::getInstance('Uri');
        $RTR = &Registry::getInstance('Router');
        $RTR->setRequest($request);

        $OUT = &Registry::getInstance('Output');
        
        Registry::getInstance('Security');
        Registry::getInstance('Language');

        // 4. Determine Route
        $route = self::checkRequest($RTR, $request);

        // 5. Dispatch
        $response = self::dispatch($route, $request);

        // 6. Output
        if ($response instanceof Response) {
            $response->prepare();
            $response->send();
        } else {
            $OUT->display();
        }
    }

    /**
     * Initialize Environment
     */
    protected static function init()
    {

        // Set Default Charset to UTF-8
        ini_set('default_charset', 'UTF-8');
        ini_set('php.internal_encoding', 'UTF-8');
        
        if (!defined('MB_ENABLED')) define('MB_ENABLED', extension_loaded('mbstring'));
        if (!defined('ICONV_ENABLED')) define('ICONV_ENABLED', extension_loaded('iconv'));
        
        if (MB_ENABLED) {
            @ini_set('mbstring.internal_encoding', 'UTF-8');
            mb_substitute_character('none');
        }
    }

    /**
     * Check Request and determine Route
     */
    protected static function checkRequest($RTR, $request)
    {
        $class = ucfirst((string)$RTR->class);
        $namespace = 'Root\\Pages\\';
        
        if (!empty($RTR->directory)) {
            $namespace .= str_replace('/', '\\', trim((string)$RTR->directory, '/')) . '\\';
        }
        
        $fqcn = $namespace . $class;
        $requestMethod = $request->server->get('REQUEST_METHOD', 'GET');
        $method = $RTR->method ?: (($requestMethod === 'POST') ? 'post' : 'index');

        // Global Auth Check
        $path = $request->getPathInfo();
        $isPublic = ($path === '/' || $path === '' || $path === '/post/user/login' || $path === '/post/user' || $path === '/login' || $path === '/post/user/logout');
        
        if (!$isPublic) {
            $auth = Registry::getInstance('Auth');
            if (!$auth->isLoggedIn()) {
                if (strpos($path, '/post/') === 0) {
                    $response = new Response();
                    $response->json([
                        'success' => false,
                        'message' => 'Unauthorized'
                    ], 200);
                } else {
                    $url = Registry::getInstance('Url');
                    $url->redirect('/');
                }
            }
        }

        if (empty($class) || !class_exists($fqcn) || !method_exists($fqcn, $method)) {
            $error = "Route not found: $path";
            error_log($error);
            $response = new Response();
            $response->json([
                'success' => false,
                'message' => $error
            ], 200);
        }


        
        return [
            'class' => $fqcn,
            'method' => $method,
            'params' => $RTR->getParams()
        ];
    }

    /**
     * Dispatch to Route
     */
    protected static function dispatch($route, Request $request)
    {
        $class = $route['class'];
        $method = $route['method'];
        $params = $route['params'];

        $controller = new $class();

        $reflection = new \ReflectionMethod($controller, $method);
        foreach ($reflection->getParameters() as $param) {
            if ($param->getType() && $param->getType()->getName() === Request::class) {
                array_unshift($params, $request);
                break;
            }
        }

        return call_user_func_array([$controller, $method], $params);
    }
}
