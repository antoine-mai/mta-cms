<?php namespace Admin\Services;

use Admin\Core\Registry;

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
     * Returns a baseUrl with the site_index appended.
     *
     * @param	string|string[]	$uri	URI string or an array of segments
     * @param	string	$protocol
     * @return	string
     */
    public function siteUrl($uri = '', $protocol = null)
    {
        return Registry::getInstance('Config')->siteUrl($uri, $protocol);
    }

    /**
     * Base URL
     *
     * Returns baseUrl [. 'index_page']
     *
     * @param	string|string[]	$uri	URI string or an array of segments
     * @param	string	$protocol
     * @return	string
     */
    public function baseUrl($uri = '', $protocol = null)
    {
        return Registry::getInstance('Config')->baseUrl($uri, $protocol);
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
        $config = Registry::getInstance('Config');
        $uri = Registry::getInstance('Uri');
        return $config->siteUrl($uri->uriString());
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
