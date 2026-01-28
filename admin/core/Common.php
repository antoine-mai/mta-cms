<?php namespace Admin\Core;

class Common
{
    private static $_is_php;
    private static $_classes = [];
    private static $_is_loaded = [];
    private static $config;
    private static $_mimes;

    public static function is_php($version)
    {
        $version = (string) $version;
        if ( ! isset(self::$_is_php[$version]))
        {
            self::$_is_php[$version] = version_compare(PHP_VERSION, $version, '>=');
        }
        return self::$_is_php[$version];
    }

    public static function is_really_writable($file)
    {
        if (DIRECTORY_SEPARATOR === '/' && (self::is_php('5.4') OR ! ini_get('safe_mode')))
        {
            return is_writable($file);
        }
        if (is_dir($file))
        {
            $file = rtrim($file, '/').'/'.md5(mt_rand());
            if (($fp = @fopen($file, 'ab')) === FALSE)
            {
                return FALSE;
            }
            fclose($fp);
            @chmod($file, 0777);
            @unlink($file);
            return TRUE;
        }
        elseif ( ! is_file($file) OR ($fp = @fopen($file, 'ab')) === FALSE)
        {
            return FALSE;
        }
        fclose($fp);
        return TRUE;
    }

    public static function config_item($item)
    {
        $config =& Registry::getInstance('Config', 'core');
        return $config->item($item);
    }

    public static function &get_mimes()
    {
        if (empty(self::$_mimes))
        {
            $file_path = CONFPATH.'mimes.yaml';
            if (file_exists($file_path)) {
                $yaml = new \Admin\Services\Yaml();
                self::$_mimes = $yaml->parse($file_path);
            } else {
                self::$_mimes = [];
            }
        }
        return self::$_mimes;
    }

    public static function is_https()
    {
        if ( ! empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
        {
            return TRUE;
        }
        elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
        {
            return TRUE;
        }
        elseif ( ! empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off')
        {
            return TRUE;
        }
        return FALSE;
    }

    public static function is_cli()
    {
        return (PHP_SAPI === 'cli' OR defined('STDIN'));
    }

    public static function remove_invisible_characters($str, $url_encoded = TRUE)
    {
        $non_displayables = [];
        if ($url_encoded)
        {
            $non_displayables[] = '/%0[0-8bcef]/i';	// url encoded 00-08, 11, 12, 14, 15
            $non_displayables[] = '/%1[0-9a-f]/i';	// url encoded 16-31
            $non_displayables[] = '/%7f/i';	// url encoded 127
        }
        $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127
        do
        {
            $str = preg_replace($non_displayables, '', $str, -1, $count);
        }
        while ($count);
        return $str;
    }

    public static function html_escape($var, $double_encode = TRUE)
    {
        if (empty($var))
        {
            return $var;
        }
        if (is_array($var))
        {
            foreach (array_keys($var) as $key)
            {
                $var[$key] = self::html_escape($var[$key], $double_encode);
            }
            return $var;
        }
        return htmlspecialchars($var, ENT_QUOTES, self::config_item('charset'), $double_encode);
    }

    public static function _stringify_attributes($attributes, $js = FALSE)
    {
        if (empty($attributes))
        {
            return NULL;
        }
        if (is_string($attributes))
        {
            return ' '.$attributes;
        }
        $attributes = (array) $attributes;
        $atts = '';
        foreach ($attributes as $key => $val)
        {
            $atts .= ($js) ? $key.'='.$val.',' : ' '.$key.'="'.$val.'"';
        }
        return rtrim($atts, ',');
    }

    public static function function_usable($function_name)
    {
        static $_suhosin_func_blacklist;
        if (function_exists($function_name))
        {
            if ( ! isset($_suhosin_func_blacklist))
            {
                $_suhosin_func_blacklist = extension_loaded('suhosin')
                    ? explode(',', trim(ini_get('suhosin.executor.func.blacklist')))
                    : [];
            }
            return ! in_array($function_name, $_suhosin_func_blacklist, TRUE);
        }
        return FALSE;
    }

    public static function redirect($uri = '', $method = 'auto', $code = NULL)
    {
        if ( ! preg_match('#^(\w+:)?//#i', $uri))
        {
            $CI =& get_instance();
            $uri = $CI->config->site_url($uri);
        }

        if ($method === 'auto' && isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== FALSE)
        {
            $method = 'refresh';
        }
        elseif ($method !== 'refresh' && (empty($code) OR ! is_numeric($code)))
        {
            if (isset($_SERVER['SERVER_PROTOCOL'], $_SERVER['REQUEST_METHOD']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.1')
            {
                $code = ($_SERVER['REQUEST_METHOD'] !== 'GET')
                    ? 303 // reference: http://en.wikipedia.org/wiki/Post/Redirect/Get
                    : 307;
            }
            else
            {
                $code = 302;
            }
        }

        switch ($method)
        {
            case 'refresh':
                header('Refresh:0;url='.$uri);
                break;
            default:
                header('Location: '.$uri, TRUE, $code);
                break;
        }
        exit;
    }

    public static function json_response($data = null, $status = 200, $headers = [])
    {
        return new \Admin\Core\Response\JsonResponse($data, $status, $headers);
    }
}
