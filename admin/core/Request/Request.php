<?php namespace Admin\Core\Request;

class Request
{
    /**
     * @var ParameterBag
     */
    public $query;

    /**
     * @var ParameterBag
     */
    public $request;

    /**
     * @var ParameterBag
     */
    public $attributes;

    /**
     * @var ParameterBag
     */
    public $cookies;

    /**
     * @var ParameterBag
     */
    public $files;

    /**
     * @var ParameterBag
     */
    public $server;

    /**
     * @var HeaderBag
     */
    /**
     * @var string
     */
    protected $pathInfo;

    /**
     * @var string
     */
    protected $requestUri;

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var string
     */
    protected $basePath;

    /**
     * @var HeaderBag
     */
    public $headers;

    /**
     * @var string
     */
    protected $clientIp;

    /**
     * @param array           $query      The GET parameters
     * @param array           $request    The POST parameters
     * @param array           $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
     * @param array           $cookies    The COOKIE parameters
     * @param array           $files      The FILES parameters
     * @param array           $server     The SERVER parameters
     * @param string|resource $content    The raw body data
     */
    public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        $this->initialize($query, $request, $attributes, $cookies, $files, $server, $content);
    }

    /**
     * Sets the parameters for this request.
     *
     * @param array           $query      The GET parameters
     * @param array           $request    The POST parameters
     * @param array           $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
     * @param array           $cookies    The COOKIE parameters
     * @param array           $files      The FILES parameters
     * @param array           $server     The SERVER parameters
     * @param string|resource $content    The raw body data
     */
    public function initialize(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        $this->request = new ParameterBag($request);
        $this->query = new ParameterBag($query);
        $this->attributes = new ParameterBag($attributes);
        $this->cookies = new ParameterBag($cookies);
        $this->files = new ParameterBag($files);
        $this->server = new ParameterBag($server);
        $this->headers = new HeaderBag($this->getHeadersFromServer($server));

        // @todo: Handle content if needed
    }

    /**
     * Creates a new request with values from PHP's super globals.
     *
     * @return static
     */
    public static function createFromGlobals()
    {
        $request = new static($_GET, $_POST, [], $_COOKIE, $_FILES, $_SERVER);

        if (0 === strpos($request->headers->get('CONTENT_TYPE', ''), 'application/x-www-form-urlencoded')
            && in_array(strtoupper($request->server->get('REQUEST_METHOD', 'GET')), ['PUT', 'DELETE', 'PATCH'])
        ) {
            parse_str($request->getContent(), $data);
            $request->request = new ParameterBag($data);
        }

        return $request;
    }

    /**
     * Returns the path being requested relative to the executed script.
     *
     * The path info always starts with a /.
     *
     * Suppose this request is instantiated from /mysite on localhost:
     *
     *  * http://localhost/mysite              returns an empty string
     *  * http://localhost/mysite/about        returns '/about'
     *  * http://localhost/mysite/enco%20ded   returns '/enco ded'
     *  * http://localhost/mysite/about?var=1  returns '/about'
     *
     * @return string The raw path (i.e. not urldecoded)
     */
    public function getPathInfo()
    {
        if (null === $this->pathInfo) {
            $this->pathInfo = $this->preparePathInfo();
        }

        return $this->pathInfo;
    }

    protected function preparePathInfo()
    {
        $requestUri = $this->getRequestUri();

        if (null === $requestUri) {
            return '/';
        }

        // Remove the query string
        if ($pos = strpos($requestUri, '?')) {
            $requestUri = substr($requestUri, 0, $pos);
        }

        if (null !== $this->baseUrl && false === $this->pathInfo = substr($requestUri, strlen($this->baseUrl))) {
            // If substr() returns false then PATH_INFO is set to an empty string
            return '/';
        } elseif (null === $this->baseUrl) {
            return $requestUri;
        }

        return (string) $this->pathInfo;
    }

    public function getRequestUri()
    {
        if (null === $this->requestUri) {
            $this->requestUri = $this->prepareRequestUri();
        }

        return $this->requestUri;
    }

    protected function prepareRequestUri()
    {
        $requestUri = '';

        if ($this->headers->has('X_ORIGINAL_URL')) {
            // IIS with Microsoft Rewrite Module
            $requestUri = $this->headers->get('X_ORIGINAL_URL');
            $this->headers->remove('X_ORIGINAL_URL');
            $this->server->remove('HTTP_X_ORIGINAL_URL');
            $this->server->remove('UNENCODED_URL');
            $this->server->remove('IIS_WasUrlRewritten');
        } elseif ($this->headers->has('X_REWRITE_URL')) {
            // IIS with ISAPI_Rewrite
            $requestUri = $this->headers->get('X_REWRITE_URL');
            $this->headers->remove('X_REWRITE_URL');
        } elseif ($this->server->get('IIS_WasUrlRewritten') == '1' && $this->server->get('UNENCODED_URL') != '') {
            // IIS7 with URL Rewrite: make sure we get the unencoded URL (this is the general case)
            $requestUri = $this->server->get('UNENCODED_URL');
            $this->server->remove('UNENCODED_URL');
            $this->server->remove('IIS_WasUrlRewritten');
        } elseif ($this->server->has('REQUEST_URI')) {
            $requestUri = $this->server->get('REQUEST_URI');
            // HTTP proxy reqs setup request URI with scheme and host [and port] + the path info, only use the path info
            $schemeAndHttpHost = $this->getSchemeAndHttpHost();
            if (strpos($requestUri, $schemeAndHttpHost) === 0) {
                $requestUri = substr($requestUri, strlen($schemeAndHttpHost));
            }
        } elseif ($this->server->has('ORIG_PATH_INFO')) {
            // IIS 5.0, PHP as CGI
            $requestUri = $this->server->get('ORIG_PATH_INFO');
            if ('' != $this->server->get('QUERY_STRING')) {
                $requestUri .= '?'.$this->server->get('QUERY_STRING');
            }
            $this->server->remove('ORIG_PATH_INFO');
        }

        return $requestUri;
    }

    public function getSchemeAndHttpHost()
    {
        return $this->getScheme().'://'.$this->getHttpHost();
    }

    public function getScheme()
    {
        return $this->isSecure() ? 'https' : 'http';
    }

    public function isSecure()
    {
        $https = $this->server->get('HTTPS');
        return !empty($https) && 'off' !== strtolower($https);
    }

    public function getHttpHost()
    {
        $scheme = $this->getScheme();
        $port = $this->getPort();

        if (('http' == $scheme && $port == 80) || ('https' == $scheme && $port == 443)) {
            return $this->getHost();
        }

        return $this->getHost().':'.$port;
    }

    public function getHost()
    {
        return $this->server->get('SERVER_NAME');
    }

    public function getPort()
    {
        return $this->server->get('SERVER_PORT');
    }

    /**
     * Returns the raw body data.
     */
    public function getContent($asResource = false)
    {
        // Simple implementation for now
        return file_get_contents('php://input');
    }

    private function getHeadersFromServer(array $server)
    {
        $headers = [];
        foreach ($server as $key => $value) {
            if (0 === strpos($key, 'HTTP_')) {
                $headers[substr($key, 5)] = $value;
            } elseif (in_array($key, ['CONTENT_LENGTH', 'CONTENT_MD5', 'CONTENT_TYPE'])) {
                $headers[$key] = $value;
            }
        }

        // Authorization header fallback for Apache/FPM
        if (isset($server['PHP_AUTH_USER'])) {
            $headers['AUTHORIZATION'] = 'Basic ' . base64_encode($server['PHP_AUTH_USER'] . ':' . $server['PHP_AUTH_PW']);
        }

        return $headers;
    }
    
    /**
     * Gets a "parameter" value from any bag.
     *
     * This method is mainly useful for libraries that want to provide some flexibility.
     *
     * Order of precedence: PATH (attributes), GET, BODY
     *
     * @param string $key     The key
     * @param mixed  $default The default value
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if ($this !== $result = $this->attributes->get($key, $this)) {
            return $result;
        }

        if ($this !== $result = $this->query->get($key, $this)) {
            return $result;
        }

        if ($this !== $result = $this->request->get($key, $this)) {
            return $result;
        }

        return $default;
    }


    /**
     * Checks if the request was an AJAX request.
     *
     * @return bool
     */
    public function isXmlHttpRequest()
    {
        return 'xmlhttprequest' == strtolower($this->headers->get('X-Requested-With'));
    }

    /**
     * User Agent
     *
     * @return string|null
     */
    public function getUserAgent()
    {
        return $this->headers->get('User-Agent');
    }

    /**
     * Validate IP Address
     *
     * @param	string	$ip	IP address
     * @param	string	$which	IP protocol: 'ipv4' or 'ipv6'
     * @return	bool
     */
    public function isValidIp($ip, $which = '')
    {
        switch (strtolower($which))
        {
            case 'ipv4':
                $which = FILTER_FLAG_IPV4;
                break;
            case 'ipv6':
                $which = FILTER_FLAG_IPV6;
                break;
            default:
                $which = 0;
                break;
        }

        return (bool) filter_var($ip, FILTER_VALIDATE_IP, $which);
    }

    /**
     * Fetch the IP Address
     *
     * Determines and validates the visitor's IP address.
     *
     * @return	string	IP address
     */
    public function getClientIp()
    {
        if ($this->clientIp !== NULL)
        {
            return $this->clientIp;
        }

        $config = \Admin\Core\Registry::getInstance('Config', 'core');
        $proxy_ips = $config->item('proxy_ips');

        if ( ! empty($proxy_ips) && ! is_array($proxy_ips))
        {
            $proxy_ips = explode(',', str_replace(' ', '', $proxy_ips));
        }

        $this->clientIp = $this->server->get('REMOTE_ADDR');

        if ($proxy_ips)
        {
            foreach (['HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'HTTP_X_CLIENT_IP', 'HTTP_X_CLUSTER_CLIENT_IP'] as $header)
            {
                if (($spoof = $this->server->get($header)) !== NULL)
                {
                    sscanf($spoof, '%[^,]', $spoof);

                    if ( ! $this->isValidIp($spoof))
                    {
                        $spoof = NULL;
                    }
                    else
                    {
                        break;
                    }
                }
            }

            if ($spoof)
            {
                for ($i = 0, $c = count($proxy_ips); $i < $c; $i++)
                {
                    if (strpos($proxy_ips[$i], '/') === FALSE)
                    {
                        if ($proxy_ips[$i] === $this->clientIp)
                        {
                            $this->clientIp = $spoof;
                            break;
                        }
                        continue;
                    }

                    isset($separator) OR $separator = $this->isValidIp($this->clientIp, 'ipv6') ? ':' : '.';

                    if (strpos($proxy_ips[$i], $separator) === FALSE)
                    {
                        continue;
                    }

                    if ( ! isset($ip, $sprintf))
                    {
                        if ($separator === ':')
                        {
                            $ip = explode(':',
                                str_replace('::',
                                    str_repeat(':', 9 - substr_count($this->clientIp, ':')),
                                    $this->clientIp
                                )
                            );
                            for ($j = 0; $j < 8; $j++)
                            {
                                $ip[$j] = intval($ip[$j], 16);
                            }
                            $sprintf = '%016b%016b%016b%016b%016b%016b%016b%016b';
                        }
                        else
                        {
                            $ip = explode('.', $this->clientIp);
                            $sprintf = '%08b%08b%08b%08b';
                        }

                        $ip = vsprintf($sprintf, $ip);
                    }

                    sscanf($proxy_ips[$i], '%[^/]/%d', $netaddr, $masklen);

                    if ($separator === ':')
                    {
                        $netaddr = explode(':', str_replace('::', str_repeat(':', 9 - substr_count($netaddr, ':')), $netaddr));
                        for ($j = 0; $j < 8; $j++)
                        {
                            $netaddr[$j] = intval($netaddr[$j], 16);
                        }
                    }
                    else
                    {
                        $netaddr = explode('.', $netaddr);
                    }

                    if (strncmp($ip, vsprintf($sprintf, $netaddr), $masklen) === 0)
                    {
                        $this->clientIp = $spoof;
                        break;
                    }
                }
            }
        }

        if ( ! $this->isValidIp($this->clientIp))
        {
            return $this->clientIp = '0.0.0.0';
        }

        return $this->clientIp;
    }
}
