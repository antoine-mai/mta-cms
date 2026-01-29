<?php namespace Admin\Core;

/**
 * 
**/
use \Admin\Core\Config;
use \Admin\Core\Uri;
use \Admin\Core\Controller;
use \Admin\Core\Output;
use \Admin\Core\Security;
use \Admin\Core\Language;
use \Admin\Core\Loader;

/**
 * Template Class
 *
 * Responsible for loading views and processing templates.
 */
class Template
{
    /**
     * List of paths to load views from
     *
     * @var array
     */
    protected $viewPaths = [ADMIN_ROOT . 'template/' => true];

    /**
     * Cached variables for views
     *
     * @var array
     */
    protected $cachedVars = [];

    /**
     * Nesting level of the output buffering mechanism
     *
     * @var int
     */
    protected $obLevel;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Uri
     */
    protected $uri;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var Output
     */
    protected $output;

    /**
     * @var Security
     */
    protected $security;

    /**
     * @var Language
     */
    protected $lang;

    /**
     * @var Loader
     */
    protected $load;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->obLevel = ob_get_level();
        
        // Inject core components for use in views ($this->config, $this->load, etc.)
        $this->config   = &Registry::getInstance('Config');
        $this->uri      = &Registry::getInstance('Uri');
        $this->router   = &Registry::getInstance('Router');
        $this->output   = &Registry::getInstance('Output');
        $this->security = &Registry::getInstance('Security');
        $this->lang     = &Registry::getInstance('Language');
        $this->load     = &Registry::getInstance('Loader');

        Error::logMessage('info', 'Template Class Initialized');
    }

    /**
     * Load view
     *
     * @param	string	$view
     * @param	array	$vars
     * @param	bool	$return
     * @return	string|Template
     */
    public function load($view, $vars = [], $return = false)
    {
        return $this->internalLoad(['view' => $view, 'vars' => $this->prepareVars($vars), 'return' => $return]);
    }

    /**
     * Internal load method
     *
     * @param	array	$data
     * @return	string|Template
     */
    protected function internalLoad($data)
    {
        foreach (['view', 'vars', 'path', 'return'] as $val) {
            $$val = isset($data[$val]) ? $data[$val] : false;
        }

        $fileExists = false;
        if (is_string($path) && $path !== '') {
            $x = explode('/', $path);
            $file = end($x);
        } else {
            $ext = pathinfo((string)$view, PATHINFO_EXTENSION);
            $file = ($ext === '') ? $view . '.php' : $view;
            foreach ($this->viewPaths as $viewFile => $cascade) {
                if (file_exists($viewFile . $file)) {
                    $path = $viewFile . $file;
                    $fileExists = true;
                    break;
                }
                if (!$cascade) {
                    break;
                }
            }
        }

        if (!$fileExists && !file_exists((string)$path)) {
            Error::showError('Unable to load the requested file: ' . $file);
        }

        empty($vars) or $this->cachedVars = array_merge($this->cachedVars, $vars);
        extract($this->cachedVars);

        ob_start();

        include($path);

        Error::logMessage('info', 'File loaded: ' . $path);

        if ($return === true) {
            $buffer = ob_get_contents();
            @ob_end_clean();
            return $buffer;
        }

        if (ob_get_level() > $this->obLevel + 1) {
            ob_end_flush();
        } else {
            if (isset($this->output)) {
                $this->output->appendOutput(ob_get_contents());
            } else {
                echo ob_get_contents();
            }

            @ob_end_clean();
        }

        return $this;
    }

    /**
     * Prepare variables for views
     *
     * @param	mixed	$vars
     * @return	array
     */
    protected function prepareVars($vars)
    {
        if (!is_array($vars)) {
            $vars = is_object($vars) ? get_object_vars($vars) : [];
        }

        foreach (array_keys($vars) as $key) {
            if (strncmp((string)$key, 'mta_', 4) === 0) {
                unset($vars[$key]);
            }
        }
        return $vars;
    }
}
