<?php namespace Admin\Services;
/**
 * 
**/
class Url
{
    /**
     * Site URL
     *
     * Returns a base_url with the site_index appended. This function is helpy
     * because it lets you create URLs in your content without having to worry
     * about whether or not to include the "index.php" file or your base link.
     *
     * @param	string|string[]	$uri	URI string or an array of segments
     * @param	string	$protocol
     * @return	string
     */
    public function site_url($uri = '', $protocol = NULL)
    {
        return get_instance()->config->site_url($uri, $protocol);
    }

    /**
     * Base URL
     *
     * Returns base_url [. 'index_page']
     *
     * @param	string|string[]	$uri	URI string or an array of segments
     * @param	string	$protocol
     * @return	string
     */
    public function base_url($uri = '', $protocol = NULL)
    {
        return get_instance()->config->base_url($uri, $protocol);
    }

    /**
     * Current URL
     *
     * Returns the full URL (including segments) of the page where this
     * function is placed
     *
     * @return	string
     */
    public function current_url()
    {
        $CI =& get_instance();
        return $CI->config->site_url($CI->uri->uri_string());
    }

    /**
     * URI String
     *
     * Returns the URI segments.
     *
     * @return	string
     */
    public function uri_string()
    {
        return get_instance()->uri->uri_string();
    }

    /**
     * Header Redirect
     *
     * @param	string	$uri	URL
     * @param	string	$method	Redirect method ('auto', 'location' or 'refresh')
     * @param	int	$code	HTTP Response status code
     * @return	void
     */
    public function redirect($uri = '', $method = 'auto', $code = NULL)
    {
        if ( ! preg_match('#^(\w+:)?//#i', $uri))
        {
            $uri = $this->site_url($uri);
        }

        if ($method === 'auto' && isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== FALSE)
        {
            $method = 'refresh';
        }
        elseif ($method !== 'refresh' && (empty($code) OR ! is_numeric($code)))
        {
            if (isset($_SERVER['SERVER_PROTOCOL'], $_SERVER['REQUEST_METHOD']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.1')
            {
                $code = ($_SERVER['REQUEST_METHOD'] !== 'GET') ? 303 : 307;
            }
            else
            {
                $code = 302;
            }
        }

        if ($method === 'refresh')
        {
            header('Refresh:0;url='.$uri);
        }
        else
        {
            header('Location: '.$uri, TRUE, $code);
        }
        exit;
    }
}
