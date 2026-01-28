<?php namespace Admin\Core;

defined('ADMIN_ROOT') or exit('No direct script access allowed');

/**
 * Input Class
 *
 * Pre-processes global input data and provides helper methods for accessing it.
 */
#[\AllowDynamicProperties]
class Input
{
    /**
     * IP address of the current user
     *
     * @var string
     */
    protected $ipAddress = false;

    /**
     * Whether to allow getting the GET array
     *
     * @var bool
     */
    protected $allowGetArray = true;

    /**
     * Whether to standardize newlines
     *
     * @var bool
     */
    protected $standardizeNewlines;

    /**
     * List of all HTTP request headers
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Raw input stream data
     *
     * @var string
     */
    protected $rawInputStream;

    /**
     * Parsed input stream data
     *
     * @var array
     */
    protected $inputStreamData;

    /**
     * Security class reference
     *
     * @var Security
     */
    protected $security;

    /**
     * Utf8 class reference
     *
     * @var Utf8
     */
    protected $uni;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->allowGetArray = (Common::configItem('allow_get_array') !== false);
        $this->standardizeNewlines = (bool)Common::configItem('standardize_newlines');

        $this->security = &Registry::getInstance('Security', 'core');
        if (UTF8_ENABLED === true) {
            $this->uni = &Registry::getInstance('Utf8');
        }

        Error::logMessage('info', 'Input Class Initialized');
    }

    /**
     * Fetch from array
     *
     * @param	array	&$array		Array to fetch from
     * @param	string	$index		Index to fetch
     * @return	mixed
     */
    protected function fetchFromArray(&$array, $index = null)
    {
        isset($index) or $index = array_keys($array);

        if (is_array($index)) {
            $output = [];
            foreach ($index as $key) {
                $output[$key] = $this->fetchFromArray($array, $key);
            }
            return $output;
        }

        if (isset($array[$index])) {
            $value = $array[$index];
        } elseif (($count = preg_match_all('/(?:^[^\[]+)|\[[^]]*\]/', $index, $matches)) > 1) {
            $value = $array;
            for ($i = 0; $i < $count; $i++) {
                $key = trim($matches[0][$i], '[]');
                if ($key === '') {
                    break;
                }
                if (isset($value[$key])) {
                    $value = $value[$key];
                } else {
                    return null;
                }
            }
        } else {
            return null;
        }

        return $value;
    }

    /**
     * Fetch an item from the GET array
     *
     * @param	string	$index		Index to fetch
     * @return	mixed
     */
    public function get($index = null)
    {
        return $this->fetchFromArray($_GET, $index);
    }

    /**
     * Fetch an item from the POST array
     *
     * @param	string	$index		Index to fetch
     * @return	mixed
     */
    public function post($index = null)
    {
        return $this->fetchFromArray($_POST, $index);
    }

    /**
     * Fetch an item from POST data with fallback to GET
     *
     * @param	string	$index		Index to fetch
     * @return	mixed
     */
    public function postGet($index)
    {
        return isset($_POST[$index])
            ? $this->post($index)
            : $this->get($index);
    }

    /**
     * Fetch an item from GET data with fallback to POST
     *
     * @param	string	$index		Index to fetch
     * @return	mixed
     */
    public function getPost($index)
    {
        return isset($_GET[$index])
            ? $this->get($index)
            : $this->post($index);
    }

    /**
     * Fetch an item from the COOKIE array
     *
     * @param	string	$index		Index to fetch
     * @return	mixed
     */
    public function cookie($index = null)
    {
        return $this->fetchFromArray($_COOKIE, $index);
    }

    /**
     * Fetch an item from the SERVER array
     *
     * @param	string	$index		Index to fetch
     * @return	mixed
     */
    public function server($index)
    {
        return $this->fetchFromArray($_SERVER, $index);
    }

    /**
     * Fetch an item from the php://input stream
     *
     * @param	string	$index		Index to fetch
     * @return	mixed
     */
    public function inputStream($index = null)
    {
        if (!is_array($this->inputStreamData)) {
            parse_str($this->rawInputStream, $this->inputStreamData);
            is_array($this->inputStreamData) or $this->inputStreamData = [];
        }

        return $this->fetchFromArray($this->inputStreamData, $index);
    }

    /**
     * Set cookie
     *
     * @param	mixed	$name		Cookie name or array of options
     * @param	string	$value		Cookie value
     * @param	int	$expire		Cookie expiration time
     * @param	string	$domain		Cookie domain
     * @param	string	$path		Cookie path
     * @param	string	$prefix		Cookie name prefix
     * @param	bool	$secure		Whether to only transmit cookie over HTTPS
     * @param	bool	$httponly	Whether to hide the cookie from JavaScript
     * @param	string	$samesite	SameSite attribute
     * @return	void
     */
    public function setCookie($name, $value = '', $expire = '', $domain = '', $path = '/', $prefix = '', $secure = null, $httponly = null, $samesite = null)
    {
        if (is_array($name)) {
            foreach (['value', 'expire', 'domain', 'path', 'prefix', 'secure', 'httponly', 'name', 'samesite'] as $item) {
                if (isset($name[$item])) {
                    $$item = $name[$item];
                }
            }
        }

        if ($prefix === '' && Common::configItem('cookie_prefix') !== '') {
            $prefix = Common::configItem('cookie_prefix');
        }

        if ($domain == '' && Common::configItem('cookie_domain') != '') {
            $domain = Common::configItem('cookie_domain');
        }

        if ($path === '/' && Common::configItem('cookie_path') !== '/') {
            $path = Common::configItem('cookie_path');
        }

        $secure = ($secure === null && Common::configItem('cookie_secure') !== null)
            ? (bool)Common::configItem('cookie_secure')
            : (bool)$secure;

        $httponly = ($httponly === null && Common::configItem('cookie_httponly') !== null)
            ? (bool)Common::configItem('cookie_httponly')
            : (bool)$httponly;

        if (!is_numeric($expire)) {
            $expire = time() - 86500;
        } else {
            $expire = ($expire > 0) ? time() + $expire : 0;
        }

        isset($samesite) or $samesite = Common::configItem('cookie_samesite');

        if (isset($samesite)) {
            $samesite = ucfirst(strtolower($samesite));
            in_array($samesite, ['Lax', 'Strict', 'None'], true) or $samesite = 'Lax';
        } else {
            $samesite = 'Lax';
        }

        if ($samesite === 'None' && !$secure) {
            Error::logMessage('error', $name . ' cookie sent with SameSite=None, but without Secure attribute.');
        }

        if (!Common::isPhp('7.3')) {
            $maxage = $expire - time();
            if ($maxage < 1) {
                $maxage = 0;
            }
            $cookieHeader = 'Set-Cookie: ' . $prefix . $name . '=' . rawurlencode($value);
            $cookieHeader .= ($expire === 0 ? '' : '; Expires=' . gmdate('D, d-M-Y H:i:s T', $expire)) . '; Max-Age=' . $maxage;
            $cookieHeader .= '; Path=' . $path . ($domain !== '' ? '; Domain=' . $domain : '');
            $cookieHeader .= ($secure ? '; Secure' : '') . ($httponly ? '; HttpOnly' : '') . '; SameSite=' . $samesite;
            header($cookieHeader);
            return;
        }

        $setcookieOptions = [
            'expires' => $expire,
            'path' => $path,
            'domain' => $domain,
            'secure' => $secure,
            'httponly' => $httponly,
            'samesite' => $samesite,
        ];

        setcookie($prefix . $name, (string)$value, $setcookieOptions);
    }
}
