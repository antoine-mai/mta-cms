<?php namespace Admin\Core;
/**
 * Controller Class
 * 
 * Base class for all route controllers.
**/
class Controller
{
    /**
     * Singleton instance
     *
     * @var Controller
     */
    private static $instance;

    /**
     * Core components
     */
    public $load;
    public $config;
    public $uri;
    public $router;
    public $output;
    public $security;
    public $lang;
    public $utf8;

    /**
     * Constructor
     */
    public function __construct()
    {
        self::$instance = &$this;

        $this->config   = &Registry::getInstance('Config');
        $this->uri      = &Registry::getInstance('Uri');
        $this->utf8     = &Registry::getInstance('Utf8');
        $this->router   = &Registry::getInstance('Router');
        $this->output   = &Registry::getInstance('Output');
        $this->security = &Registry::getInstance('Security');
        $this->lang     = &Registry::getInstance('Language');

        $this->load = &Registry::getInstance('Loader');
        
        Error::logMessage('info', 'Controller Class Initialized');
    }

    /**
     * Get singleton instance
     *
     * @return Controller
     */
    public static function &getInstance()
    {
        return self::$instance;
    }

    /**
     * Renders a view.
     *
     * @param string $view       The view name
     * @param array  $parameters An array of parameters to pass to the view
     * @param \Admin\Core\Response\Response $response A response instance
     *
     * @return \Admin\Core\Response\Response
     */
    protected function render(string $view, array $parameters = [], ?\Admin\Core\Response\Response $response = null): \Admin\Core\Response\Response
    {
        $content = (string)$this->load->template($view, $parameters, true);

        if (null === $response) {
            $response = new \Admin\Core\Response\Response();
        }

        $response->setContent($content);

        return $response;
    }

    /**
     * Returns a JsonResponse that uses json_encode.
     *
     * @param mixed $data    The response data
     * @param int   $status  The response status code
     * @param array $headers An array of response headers
     *
     * @return \Admin\Core\Response\JsonResponse
     */
    protected function json($data, int $status = 200, array $headers = []): \Admin\Core\Response\JsonResponse
    {
        return new \Admin\Core\Response\JsonResponse($data, $status, $headers);
    }

    /**
     * Returns a RedirectResponse to the given URL.
     *
     * @param string $url     The URL to redirect to
     * @param int    $status  The status code to use for the Response
     * @param array  $headers An array of response headers
     *
     * @return \Admin\Core\Response\RedirectResponse
     */
    protected function redirect(string $url, int $status = 302, array $headers = []): \Admin\Core\Response\RedirectResponse
    {
        return new \Admin\Core\Response\RedirectResponse($url, $status, $headers);
    }
}
