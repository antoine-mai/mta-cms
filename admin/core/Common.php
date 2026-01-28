<?php namespace Admin\Core;

/**
 * Common Class
 *
 * Contains common utility functions used throughout the application.
 */
class Common
{
    /**
     * Cache for PHP version checks
     *
     * @var array
     */
    private static $isPhpCache = [];

    /**
     * Mime types cache
     *
     * @var array
     */
    private static $mimes;

    /**
     * Determine if the current PHP version is at least the specified version
     *
     * @param	string	$version
     * @return	bool
     */
    public static function isPhp($version)
    {
        $version = (string)$version;
        if (!isset(self::$isPhpCache[$version])) {
            self::$isPhpCache[$version] = version_compare(PHP_VERSION, $version, '>=');
        }
        return self::$isPhpCache[$version];
    }

    /**
     * Check if a file is really writable
     *
     * @param	string	$file
     * @return	bool
     */
    public static function isReallyWritable($file)
    {
        if (DIRECTORY_SEPARATOR === '/' && (self::isPhp('5.4') || !ini_get('safe_mode'))) {
            return is_writable($file);
        }

        if (is_dir($file)) {
            $file = rtrim($file, '/') . '/' . md5((string)mt_rand());
            if (($fp = @fopen($file, 'ab')) === false) {
                return false;
            }
            fclose($fp);
            @chmod($file, 0777);
            @unlink($file);
            return true;
        } elseif (!is_file($file) || ($fp = @fopen($file, 'ab')) === false) {
            return false;
        }

        fclose($fp);
        return true;
    }

    /**
     * Get a config item
     *
     * @param	string	$item
     * @return	mixed
     */
    public static function configItem($item)
    {
        $config = &Registry::getInstance('Config', 'core');
        return $config->item($item);
    }

    /**
     * Get mime types
     *
     * @return	array
     */
    public static function &getMimes()
    {
        if (empty(self::$mimes)) {
            $filePath = CONFPATH . 'mimes.yaml';
            if (file_exists($filePath)) {
                $yaml = new \Admin\Services\Yaml();
                self::$mimes = $yaml->parse($filePath);
            } else {
                self::$mimes = [];
            }
        }
        return self::$mimes;
    }

    /**
     * Is the current connection HTTPS?
     *
     * @return	bool
     */
    public static function isHttps()
    {
        if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
            return true;
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') {
            return true;
        } elseif (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
            return true;
        }
        return false;
    }

    /**
     * Remove invisible characters from a string
     *
     * @param	string	$str
     * @param	bool	$urlEncoded
     * @return	string
     */
    public static function removeInvisibleCharacters($str, $urlEncoded = true)
    {
        $nonDisplayables = [];
        if ($urlEncoded) {
            $nonDisplayables[] = '/%0[0-8bcef]/i'; // url encoded 00-08, 11, 12, 14, 15
            $nonDisplayables[] = '/%1[0-9a-f]/i'; // url encoded 16-31
            $nonDisplayables[] = '/%7f/i'; // url encoded 127
        }
        $nonDisplayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S'; // 00-08, 11, 12, 14-31, 127
        do {
            $str = (string)preg_replace($nonDisplayables, '', (string)$str, -1, $count);
        } while ($count);
        return $str;
    }

    /**
     * HTML Escape
     *
     * @param	mixed	$var
     * @param	bool	$doubleEncode
     * @return	mixed
     */
    public static function htmlEscape($var, $doubleEncode = true)
    {
        if (empty($var)) {
            return $var;
        }
        if (is_array($var)) {
            foreach (array_keys($var) as $key) {
                $var[$key] = self::htmlEscape($var[$key], $doubleEncode);
            }
            return $var;
        }
        return htmlspecialchars((string)$var, ENT_QUOTES, (string)self::configItem('charset'), $doubleEncode);
    }
}
