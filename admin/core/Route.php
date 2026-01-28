<?php namespace Admin\Core;
/**
 * 
**/
#[\AllowDynamicProperties]
class Route
{
	private static $instance;
	public $load;
    public $config;
    public $uri;
    public $router;
    public $output;
    public $security;
    public $input;
    public $lang;
    public $utf8;

	public function __construct()
	{
		self::$instance =& $this;

        $this->config   =& \Admin\Core\Registry::getInstance('Config', 'core');
        $this->utf8     =& \Admin\Core\Registry::getInstance('Utf8');
        $this->router   =& \Admin\Core\Registry::getInstance('Router', 'core');
        $this->output   =& \Admin\Core\Registry::getInstance('Output', 'core');
        $this->security =& \Admin\Core\Registry::getInstance('Security', 'core');
        $this->input    =& \Admin\Core\Registry::getInstance('Input', 'core');
        $this->lang     =& \Admin\Core\Registry::getInstance('Lang', 'core');
        
		$this->load =& \Admin\Core\Registry::getInstance('Loader', 'core');
		$this->load->initialize();
		Error::logMessage('info', 'Route Class Initialized');
	}
	public static function &getInstance()
	{
		return self::$instance;
	}



    /**
     * Renders a view.
     *
     * @param string $view       The view name
     * @param array  $parameters An array of parameters to pass to the view
     * @param Response $response A response instance
     *
     * @return Response
     */
    protected function render(string $view, array $parameters = [], ?\Admin\Core\Response\Response $response = null): \Admin\Core\Response\Response
    {
        $content = $this->load->template($view, $parameters, true);

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
