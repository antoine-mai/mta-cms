<?php namespace Root\Core;
/**
 * Url Class
**/
class Url
{
    /**
     * Redirect to a URL
     */
    public function redirect($uri = '', $method = 'auto', $code = NULL)
    {
        if ( ! preg_match('#^https?://#i', (string)$uri))
        {
            $uri = $this->siteUrl($uri);
        }

        if ($method === 'auto' && isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== FALSE)
        {
            $method = 'refresh';
        }
        elseif ($method !== 'refresh' && (empty($code) OR ! is_numeric($code)))
        {
            if (isset($_SERVER['REQUEST_METHOD'], $_SERVER['SERVER_PROTOCOL'])
                && $_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.1')
            {
                $code = 303;
            }
            else
            {
                $code = 302;
            }
        }

        switch ($method)
        {
            case 'refresh':
                header("Refresh:0;url=".$uri);
                break;
            default:
                header("Location: ".$uri, TRUE, $code);
                break;
        }
        exit;
    }

    /**
     * Site URL
     */
    public function siteUrl($uri = '')
    {
        $config = &Registry::getInstance('Config');
        $baseUrl = $config->item('base_url') ?: '/';
        return rtrim($baseUrl, '/') . '/' . ltrim((string)$uri, '/');
    }
}
