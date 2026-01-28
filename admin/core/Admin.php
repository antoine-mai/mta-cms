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

class Admin
{
    public static function run()
    {
        // 0. Init
        self::initErrorHandlers();

        // 1. Load Config
        $CFG = self::loadConfig();

        // 2. Load Helpers, Libraries

        // 3. Load Core
        list($URI, $RTR, $OUT) = self::loadCore($CFG);

        // 4. Check Request
        $route = self::checkRequest($RTR, $URI);

        // 5. Load View (Dispatch)
        // 6. Create Response
        self::dispatch($route);

        // 7. Return Response
        $OUT->_display();
    }

    protected static function initErrorHandlers()
    {
        set_error_handler('_error_handler');
        set_exception_handler('_exception_handler');
        register_shutdown_function('_shutdown_handler');
    }

    protected static function loadConfig()
    {
        global $assign_to_config;
        if (!empty($assign_to_config['subclass_prefix'])) {
            get_config(['subclass_prefix' => $assign_to_config['subclass_prefix']]);
        }
        
        $CFG =& load_class('Config', 'core');
        
        if (isset($assign_to_config) && is_array($assign_to_config)) {
             foreach ($assign_to_config as $key => $value) {
                $CFG->set_item($key, $value);
            }
        }
        
        self::initCharset($CFG->item('charset'));
        
        return $CFG;
    }


    protected static function loadCore($CFG)
    {
        load_class('Utf8', 'core');
        $URI =& load_class('URI', 'core');
        $RTR =& load_class('Router', 'core');
        $OUT =& load_class('Output', 'core');
        
        // Cache check
        if ($OUT->_display_cache($CFG, $URI) === TRUE) {
            exit;
        }

        load_class('Security', 'core');
        load_class('Input', 'core');
        load_class('Lang', 'core');

        return [$URI, $RTR, $OUT];
    }

    protected static function checkRequest($RTR, $URI)
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
        $requestMethod = $_SERVER['REQUEST_METHOD'];
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
            show_404($fqcn . '::' . $method);
        }

        // Params: Since we are ignoring the method segment from URL (implied by HTTP verb),
        // we need to be careful. CodeIgniter's Router might have consumed 'index' or something as the method.
        // If the URL is /welcome/index, Router says class=Welcome, method=index.
        // If URL is /welcome, Router says class=Welcome, method=index (default).
        // If URL is /welcome/foo, Router says class=Welcome, method=foo.
        
        // With "Route" pattern:
        // /welcome -> Welcome class -> index() (GET)
        // /welcome (POST) -> Welcome class -> post() (POST)
        
        // What if URL is /welcome/foo ? 
        // If "Route" pattern implies 1 class = 1 endpoint, then /welcome/foo is 404 unless 'foo' is a param.
        // In legacy CI, 'foo' is method. 
        // If user says "always has index and post", it implies specific methods are creating the response.
        
        // We will assume standard Route pattern: URL -> Class. Method is internal decision.
        // Params are subsequent segments.
        
        $params = array_slice($URI->rsegments, 2);

        return [
            'class' => $fqcn,
            'method' => $method,
            'params' => $params
        ];
    }

    protected static function dispatch($route)
    {
        $class = $route['class'];
        $method = $route['method'];
        $params = $route['params'];

        $CI = new $class();
        call_user_func_array([&$CI, $method], $params);
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

        if (is_php('5.6')) {
            ini_set('php.internal_encoding', $charset);
        }
    }
}
