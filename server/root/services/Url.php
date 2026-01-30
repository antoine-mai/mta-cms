<?php namespace Root\Services;

use Root\Core\Registry;

/**
 * Url Class
 * 
 * Provides helper methods for URL management.
 */
class Url
{
    /**
     * Site URL
     *
     * Returns baseUrl . index_page . uri
     *
     * @param	string|string[]	$uri	URI string or an array of segments
     * @param	string	$protocol
     * @return	string
     */
    public function siteUrl($uri = '', $protocol = null)
    {
        $config = Registry::getInstance('Config');
        $baseUrl = $config->slashItem('baseUrl');

        if ($baseUrl === '') {
            $request = Registry::getInstance('Request');
            $serverName = $_SERVER['SERVER_NAME'] ?? 'localhost';
            $serverPort = $_SERVER['SERVER_PORT'] ?? 80;
            $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
            
            $port = (($scheme === 'http' && $serverPort == 80) || ($scheme === 'https' && $serverPort == 443)) ? '' : ':' . $serverPort;
            $baseUrl = $scheme . '://' . $serverName . $port . $basePath . '/';
        }

        if (isset($protocol)) {
            if ($protocol === '') {
                $baseUrl = substr($baseUrl, strpos($baseUrl, '//'));
            } else {
                $baseUrl = $protocol . substr($baseUrl, strpos($baseUrl, '://'));
            }
        }

        if (empty($uri)) {
            return $baseUrl . $config->item('index_page');
        }

        $uri = $this->_uriString($uri);

        if ($config->item('enableQueryStrings') === false) {
            $suffix = $config->item('url_suffix') ?? '';

            if ($suffix !== '') {
                if (($offset = strpos($uri, '?')) !== false) {
                    $uri = substr($uri, 0, $offset) . $suffix . substr($uri, $offset);
                } else {
                    $uri .= $suffix;
                }
            }

            return $baseUrl . $config->slashItem('index_page') . $uri;
        } elseif (strpos($uri, '?') === false) {
            $uri = '?' . $uri;
        }

        return $baseUrl . $config->item('index_page') . $uri;
    }

    /**
     * Base URL
     *
     * Returns baseUrl [. uri]
     *
     * @param	string|string[]	$uri	URI string or an array of segments
     * @param	string	$protocol
     * @return	string
     */
    public function baseUrl($uri = '', $protocol = null)
    {
        $config = Registry::getInstance('Config');
        $baseUrl = $config->slashItem('baseUrl');

        if (isset($protocol)) {
            if ($protocol === '') {
                $baseUrl = substr($baseUrl, strpos($baseUrl, '//'));
            } else {
                $baseUrl = $protocol . substr($baseUrl, strpos($baseUrl, '://'));
            }
        }

        return $baseUrl . $this->_uriString($uri);
    }

    /**
     * Build URI string
     *
     * @param	string|string[]	$uri	URI string or an array of segments
     * @return	string
     */
    protected function _uriString($uri)
    {
        $config = Registry::getInstance('Config');
        
        if ($config->item('enableQueryStrings') === false) {
            is_array($uri) && $uri = implode('/', $uri);
            return ltrim($uri, '/');
        } elseif (is_array($uri)) {
            return http_build_query($uri);
        }

        return $uri;
    }

    /**
     * Current URL
     *
     * Returns the full URL (including segments) of the page where this
     * function is placed
     *
     * @return	string
     */
    public function currentUrl()
    {
        $uri = Registry::getInstance('Uri');
        return $this->siteUrl($uri->uriString());
    }

    /**
     * URI String
     *
     * Returns the URI segments.
     *
     * @return	string
     */
    public function uriString()
    {
        return Registry::getInstance('Uri')->uriString();
    }

    /**
     * Header Redirect
     *
     * @param	string	$uri	URL
     * @param	string	$method	Redirect method ('auto', 'location' or 'refresh')
     * @param	int	$code	HTTP Response status code
     * @return	void
     */
    public function redirect($uri = '', $method = 'auto', $code = null)
    {
        if (!preg_match('#^(\w+:)?//#i', $uri)) {
            $uri = $this->siteUrl($uri);
        }

        if ($method === 'auto' && isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== false) {
            $method = 'refresh';
        } elseif ($method !== 'refresh' && (empty($code) || !is_numeric($code))) {
            if (isset($_SERVER['SERVER_PROTOCOL'], $_SERVER['REQUEST_METHOD']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.1') {
                $code = ($_SERVER['REQUEST_METHOD'] !== 'GET') ? 303 : 307;
            } else {
                $code = 302;
            }
        }

        if ($method === 'refresh') {
            header('Refresh:0;url=' . $uri);
        } else {
            header('Location: ' . $uri, true, (int)$code);
        }
        exit;
    }
}
