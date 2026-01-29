<?php namespace Admin\Core;
/**
 * 
**/
use \Admin\Core\Response\Response;
use \Admin\Core\Request\Request;
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
        // 1. Setup Environment
        self::init();

        // 2. Create Request
        $request = Request::createFromGlobals();
        Registry::setInstance('Request', $request);

        // 3. Load Core Components
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
        set_error_handler([Error::class, 'errorHandler']);
        set_exception_handler([Error::class, 'exceptionHandler']);
        register_shutdown_function([Error::class, 'shutdownHandler']);

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
        $namespace = 'Admin\\Pages\\';
        
        if (!empty($RTR->directory)) {
            $namespace .= str_replace('/', '\\', trim((string)$RTR->directory, '/')) . '\\';
        }
        
        $fqcn = $namespace . $class;
        $requestMethod = $request->server->get('REQUEST_METHOD', 'GET');
        $method = ($requestMethod === 'POST') ? 'post' : 'index';

        if (empty($class) || !class_exists($fqcn) || !method_exists($fqcn, $method)) {
            Error::show404($fqcn . '::' . $method);
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
