<?php namespace Admin\Core;
/**
 * 
**/
if (file_exists(ROOT_DIR . '/config/admin/' . 'constants.php')) {
    require_once(ROOT_DIR . '/config/admin/' . 'constants.php');
}
require_once(ADMIN_ROOT . 'core/Common.php');

if (!function_exists('get_instance')) {
    function &get_instance()
    {
        return \Admin\Core\Route::get_instance();
    }
}

use Admin\Core\Common;

class Admin
{
    public static function run()
    {
        // 0. Init
        self::errorHandlers();

        // 1. Create Request
        $request = \Admin\Core\Request\Request::createFromGlobals();

        // 1. Load Config
        $CFG = self::loadConfig();

        // 2. Load Helpers, Libraries

        // 3. Load Core
        list($URI, $RTR, $OUT) = self::loadCore($CFG, $request);

        // 4. Check Request
        $route = self::checkRequest($RTR, $request);

        // 5. Load View (Dispatch)
        // 6. Create Response
        $response = self::dispatch($route, $request);

        // 7. Return Response
        if ($response instanceof \Admin\Core\Response\Response) {
            $response->prepare();
            $response->send();
        } else {
            $OUT->_display();
        }
    }

    protected static function errorHandlers()
    {
        set_error_handler([\Admin\Core\Error::class, '_error_handler']);
        set_exception_handler([\Admin\Core\Error::class, '_exception_handler']);
        register_shutdown_function([\Admin\Core\Error::class, '_shutdown_handler']);
    }

    protected static function loadConfig()
    {
        global $assign_to_config;
        if (!empty($assign_to_config['subclass_prefix'])) {
            Common::get_config(['subclass_prefix' => $assign_to_config['subclass_prefix']]);
        }
        
        $CFG =& Registry::getInstance('Config', 'core');
        
        if (isset($assign_to_config) && is_array($assign_to_config)) {
             foreach ($assign_to_config as $key => $value) {
                $CFG->set_item($key, $value);
            }
        }
        
        self::initCharset($CFG->item('charset'));
        
        return $CFG;
    }


    protected static function loadCore($CFG, $request)
    {
        Registry::getInstance('Utf8');
        // $URI =& Registry::getInstance('URI', 'core'); // Deprecated, replaced by Request
        $RTR =& Registry::getInstance('Router', 'core');
        $RTR->setRequest($request);

        $OUT =& Registry::getInstance('Output', 'core');
        
        // Cache check
        // if ($OUT->_display_cache($CFG, $URI) === TRUE) {
        //     exit;
        // }

        Registry::getInstance('Security', 'core');
        Registry::getInstance('Input', 'core');
        Registry::getInstance('Lang', 'core');

        return [null, $RTR, $OUT];
    }

    protected static function checkRequest($RTR, $request)
    {
        $e404 = FALSE;
        $class = ucfirst($RTR->class);
        
        // Convert directory structure to namespace
        $namespace = 'Admin\\Routes\\';
        if (!empty($RTR->directory)) {
            $namespace .= str_replace('/', '\\', trim($RTR->directory, '/')) . '\\';
        }
        
        $fqcn = $namespace . $class; // Fully Qualified Class Name

        // Determine method based on HTTP Verb
        $requestMethod = $request->server->get('REQUEST_METHOD', 'GET');
        $method = ($requestMethod === 'POST') ? 'post' : 'index';

        if (empty($class) OR !class_exists($fqcn)) {
            $e404 = TRUE;
        } else {
            // Check if method exists in the Route class (it should, as it extends Admin\Core\Route)
            // But we check anyway in case it's missing in a specific route or abstract
            if (!method_exists($fqcn, $method)) {
                 $e404 = TRUE; 
            }
        }

        if ($e404) {
            Error::show_404($fqcn . '::' . $method);
        }
        
        // $params = array_slice($URI->rsegments, 2);
        // We need to get params from router or request now
        $params = $RTR->getParams();

        return [
            'class' => $fqcn,
            'method' => $method,
            'params' => $params
        ];
    }

    protected static function dispatch($route, \Admin\Core\Request\Request $request)
    {
        $class = $route['class'];
        $method = $route['method'];
        $params = $route['params'];

        $CI = new $class();

        // Check if the method expects a Request object
        $reflection = new \ReflectionMethod($CI, $method);
        foreach ($reflection->getParameters() as $param) {
            if ($param->getType() && $param->getType()->getName() === \Admin\Core\Request\Request::class) {
                array_unshift($params, $request);
                break;
            }
        }

        return call_user_func_array([&$CI, $method], $params);
    }

    protected static function initCharset($charset)
    {
        $charset = strtoupper($charset);
        ini_set('default_charset', $charset);

        if (extension_loaded('mbstring')) {
            define('MB_ENABLED', TRUE);
            @ini_set('mbstring.internal_encoding', $charset);
            mb_substitute_character('none');
        } else {
            define('MB_ENABLED', FALSE);
        }

        if (extension_loaded('iconv')) {
            define('ICONV_ENABLED', TRUE);
            @ini_set('iconv.internal_encoding', $charset);
        } else {
            define('ICONV_ENABLED', FALSE);
        }

        if (Common::is_php('5.6')) {
            ini_set('php.internal_encoding', $charset);
        }
    }
}
