<?php namespace Root\Core;
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
     * Returns a JsonResponse that uses json_encode.
     *
     * @param mixed $data    The response data
     * @param int   $status  The response status code
     * @param array $headers An array of response headers
     *
     * @return \Root\Core\Response\JsonResponse
     */
    protected function json($data, int $status = 200, array $headers = []): \Root\Core\Response\JsonResponse
    {
        return new \Root\Core\Response\JsonResponse($data, $status, $headers);
    }

    /**
     * Returns a RedirectResponse to the given URL.
     *
     * @param string $url     The URL to redirect to
     * @param int    $status  The status code to use for the Response
     * @param array  $headers An array of response headers
     *
     * @return \Root\Core\Response\RedirectResponse
     */
    protected function redirect(string $url, int $status = 302, array $headers = []): \Root\Core\Response\RedirectResponse
    {
        return new \Root\Core\Response\RedirectResponse($url, $status, $headers);
    }
}
