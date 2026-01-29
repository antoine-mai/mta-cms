<?php namespace Admin\Core;
if (file_exists(ROOT_DIR . '/config/admin/' . 'constants.php')) {
    require_once(ROOT_DIR . '/config/admin/' . 'constants.php');
}
/**
 * Startup Class
 *
 * Orchestrates the application flow.
 */
class Startup
{
    /**
     * Initialize Error Handlers
     */
    protected static function errorHandlers()
    {
        set_error_handler([\Admin\Core\Error::class, 'errorHandler']);
        set_exception_handler([\Admin\Core\Error::class, 'exceptionHandler']);
        register_shutdown_function([\Admin\Core\Error::class, 'shutdownHandler']);
    }

    /**
     * Run the application
     */
    public static function run()
    {
        // 0. Init
        self::errorHandlers();

        // 1. Create Request
        $request = \Admin\Core\Request\Request::createFromGlobals();
        Registry::setInstance('Request', $request);

        // 1. Load Config
        $CFG = self::loadConfig();

        // 2. Load Core
        list($URI, $RTR, $OUT) = self::loadCore($CFG, $request);

        // 3. Check Request
        $route = self::checkRequest($RTR, $request);

        // 4. Load View (Dispatch)
        $response = self::dispatch($route, $request);

        // 5. Return Response
        if ($response instanceof \Admin\Core\Response\Response) {
            $response->prepare();
            $response->send();
        } else {
            $OUT->display();
        }
    }

    /**
     * Load Configuration
     */
    protected static function loadConfig()
    {
        global $assign_to_config;
        
        $CFG = &Registry::getInstance('Config', 'core');
        
        if (isset($assign_to_config) && is_array($assign_to_config)) {
             foreach ($assign_to_config as $key => $value) {
                $CFG->setItem($key, $value);
            }
        }
        
        self::initCharset($CFG->item('charset'));
        
        return $CFG;
    }

    /**
     * Load Core Components
     */
    protected static function loadCore($CFG, $request)
    {
        Registry::getInstance('Utf8');
        $URI = &Registry::getInstance('Uri', 'core');
        $RTR = &Registry::getInstance('Router', 'core');
        $RTR->setRequest($request);

        $OUT = &Registry::getInstance('Output', 'core');
        
        Registry::getInstance('Security', 'core');
        Registry::getInstance('Language', 'core');

        return [$URI, $RTR, $OUT];
    }

    /**
     * Check Request and determine Route
     */
    protected static function checkRequest($RTR, $request)
    {
        $e404 = false;
        $class = ucfirst((string)$RTR->class);
        
        $namespace = 'Admin\\Pages\\';
        if (!empty($RTR->directory)) {
            $namespace .= str_replace('/', '\\', trim((string)$RTR->directory, '/')) . '\\';
        }
        
        $fqcn = $namespace . $class;

        $requestMethod = $request->server->get('REQUEST_METHOD', 'GET');
        $method = ($requestMethod === 'POST') ? 'post' : 'index';

        if (empty($class) || !class_exists($fqcn)) {
            $e404 = true;
        } else {
            if (!method_exists($fqcn, $method)) {
                 $e404 = true; 
            }
        }

        if ($e404) {
            Error::show404($fqcn . '::' . $method);
        }
        
        $params = $RTR->getParams();

        return [
            'class' => $fqcn,
            'method' => $method,
            'params' => $params
        ];
    }

    /**
     * Dispatch to Route
     */
    protected static function dispatch($route, \Admin\Core\Request\Request $request)
    {
        $class = $route['class'];
        $method = $route['method'];
        $params = $route['params'];

        $CI = new $class();

        $reflection = new \ReflectionMethod($CI, $method);
        foreach ($reflection->getParameters() as $param) {
            if ($param->getType() && $param->getType()->getName() === \Admin\Core\Request\Request::class) {
                array_unshift($params, $request);
                break;
            }
        }

        return call_user_func_array([&$CI, $method], $params);
    }

    /**
     * Initialize Charset
     */
    protected static function initCharset($charset)
    {
        $charset = strtoupper((string)$charset);
        if ($charset === '') $charset = 'UTF-8';
        
        ini_set('default_charset', $charset);

        if (extension_loaded('mbstring')) {
            if (!defined('MB_ENABLED')) define('MB_ENABLED', true);
            @ini_set('mbstring.internal_encoding', $charset);
            mb_substitute_character('none');
        } else {
            if (!defined('MB_ENABLED')) define('MB_ENABLED', false);
        }

        if (extension_loaded('iconv')) {
            if (!defined('ICONV_ENABLED')) define('ICONV_ENABLED', true);
            @ini_set('iconv.internal_encoding', $charset);
        } else {
            if (!defined('ICONV_ENABLED')) define('ICONV_ENABLED', false);
        }

        if (Common::isPhp('5.6')) {
            ini_set('php.internal_encoding', $charset);
        }
    }
}
