<?php namespace Admin\Services\Session;

use UnexpectedValueException;

/**
 * Session Class
 *
 * Handles session management with support for multiple drivers.
 */
class Session
{
    /**
     * User data alias for $_SESSION
     *
     * @var array
     */
    public $userdata;

    /**
     * Selected driver
     *
     * @var string
     */
    protected $driver = 'files';

    /**
     * Configuration
     *
     * @var array
     */
    protected $config;

    /**
     * Session ID regular expression
     *
     * @var string
     */
    protected $sidRegexp;

    /**
     * Constructor
     *
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        if (\Admin\Core\Console::isCli()) {
            \Admin\Core\Error::logMessage('debug', 'Session: Initialization under CLI aborted.');
            return;
        } elseif ((bool)ini_get('session.auto_start')) {
            \Admin\Core\Error::logMessage('error', 'Session: session.auto_start is enabled in php.ini. Aborting.');
            return;
        } elseif (!empty($params['driver'])) {
            $this->driver = $params['driver'];
            unset($params['driver']);
        } elseif ($driver = \Admin\Core\Common::configItem('sess_driver')) {
            $this->driver = $driver;
        } elseif (\Admin\Core\Common::configItem('sess_use_database')) {
            \Admin\Core\Error::logMessage('debug', 'Session: "sess_driver" is empty; using BC fallback to "sess_use_database".');
            $this->driver = 'database';
        }

        $class = $this->loadClasses($this->driver);
        $this->configure($params);
        $this->config['_sid_regexp'] = $this->sidRegexp;

        $driverObj = new $class($this->config);
        
        $wrapper = new Wrapper($driverObj);

        session_set_save_handler($wrapper, true);

        if (isset($_COOKIE[$this->config['cookie_name']])
            && (!is_string($_COOKIE[$this->config['cookie_name']])
                || !preg_match('#\A' . $this->sidRegexp . '\z#', $_COOKIE[$this->config['cookie_name']])
            )
        ) {
            unset($_COOKIE[$this->config['cookie_name']]);
        }

        session_start();

        if ((empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest')
            && ($regenerateTime = \Admin\Core\Common::configItem('sess_time_to_update')) > 0
        ) {
            if (!isset($_SESSION['__ci_last_regenerate'])) {
                $_SESSION['__ci_last_regenerate'] = time();
            } elseif ($_SESSION['__ci_last_regenerate'] < (time() - $regenerateTime)) {
                $this->sessRegenerate((bool)\Admin\Core\Common::configItem('sess_regenerate_destroy'));
            }
        } elseif (isset($_COOKIE[$this->config['cookie_name']]) && $_COOKIE[$this->config['cookie_name']] === session_id()) {
            $expires = empty($this->config['cookie_lifetime']) ? 0 : time() + $this->config['cookie_lifetime'];
            
            setcookie(
                $this->config['cookie_name'],
                (string)session_id(),
                [
                    'expires' => $expires,
                    'path' => $this->config['cookie_path'],
                    'domain' => $this->config['cookie_domain'],
                    'secure' => $this->config['cookie_secure'],
                    'httponly' => true,
                    'samesite' => $this->config['cookie_samesite']
                ]
            );

            if (!$this->config['cookie_secure'] && $this->config['cookie_samesite'] === 'None') {
                \Admin\Core\Error::logMessage('error', "Session: '" . $this->config['cookie_name'] . "' cookie sent with SameSite=None, but without Secure attribute.'");
            }
        }

        $this->initVars();
        \Admin\Core\Error::logMessage('info', "Session: Class initialized using '" . $this->driver . "' driver.");
    }

    /**
     * Load driver classes
     *
     * @param string $driver
     * @return string
     */
    protected function loadClasses($driver)
    {
        // Try PSR-4 first
        $class = 'Admin\\Services\\Session\\Drivers\\' . ucfirst((string)$driver);
        if (class_exists($class)) {
            return $class;
        }

        $prefix = \Admin\Core\Common::configItem('subclass_prefix');
        $class = 'Session_' . $driver . '_driver';
        
        if (!class_exists($class, false)) {
            $filePath = ADMIN_ROOT . 'services/Session/drivers/' . $class . '.php';
            if (file_exists($filePath)) {
                require_once($filePath);
            }
        }

        if (!class_exists($class, false)) {
            throw new UnexpectedValueException("Session: Configured driver '" . $driver . "' was not found. Aborting.");
        }

        $subclass = $prefix . $class;
        if (!class_exists($subclass, false)) {
            $filePath = ADMIN_ROOT . 'services/Session/drivers/' . $subclass . '.php';
            if (file_exists($filePath)) {
                require_once($filePath);
                if (class_exists($subclass, false)) {
                    return $subclass;
                }
            }
        }

        return $class;
    }

    /**
     * Configure session settings
     *
     * @param array $params
     * @return void
     */
    protected function configure(&$params)
    {
        $expiration = \Admin\Core\Common::configItem('sess_expiration');
        if (isset($params['cookie_lifetime'])) {
            $params['cookie_lifetime'] = (int)$params['cookie_lifetime'];
        } else {
            $params['cookie_lifetime'] = (!isset($expiration) && \Admin\Core\Common::configItem('sess_expire_on_close'))
                ? 0 : (int)$expiration;
        }

        isset($params['cookie_name']) or $params['cookie_name'] = \Admin\Core\Common::configItem('sess_cookie_name');
        if (empty($params['cookie_name'])) {
            $params['cookie_name'] = ini_get('session.name');
        } else {
            ini_set('session.name', (string)$params['cookie_name']);
        }

        isset($params['cookie_path']) or $params['cookie_path'] = \Admin\Core\Common::configItem('cookie_path');
        isset($params['cookie_domain']) or $params['cookie_domain'] = \Admin\Core\Common::configItem('cookie_domain');
        isset($params['cookie_secure']) or $params['cookie_secure'] = (bool)\Admin\Core\Common::configItem('cookie_secure');
        isset($params['cookie_samesite']) or $params['cookie_samesite'] = \Admin\Core\Common::configItem('sess_samesite');

        if (!isset($params['cookie_samesite'])) {
            $params['cookie_samesite'] = ini_get('session.cookie_samesite');
        }

        if (isset($params['cookie_samesite'])) {
            $params['cookie_samesite'] = ucfirst(strtolower((string)$params['cookie_samesite']));
            in_array($params['cookie_samesite'], ['Lax', 'Strict', 'None'], true) or $params['cookie_samesite'] = 'Lax';
        } else {
            $params['cookie_samesite'] = 'Lax';
        }

        session_set_cookie_params([
            'lifetime' => $params['cookie_lifetime'],
            'path'     => $params['cookie_path'],
            'domain'   => $params['cookie_domain'],
            'secure'   => $params['cookie_secure'],
            'httponly' => true,
            'samesite' => $params['cookie_samesite']
        ]);

        if (empty($expiration)) {
            $params['expiration'] = (int)ini_get('session.gc_maxlifetime');
        } else {
            $params['expiration'] = (int)$expiration;
            ini_set('session.gc_maxlifetime', (string)$expiration);
        }

        $params['match_ip'] = (bool)(isset($params['match_ip']) ? $params['match_ip'] : \Admin\Core\Common::configItem('sess_match_ip'));
        isset($params['save_path']) or $params['save_path'] = \Admin\Core\Common::configItem('sess_save_path');
        $this->config = $params;

        ini_set('session.use_trans_sid', '0');
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_cookies', '1');
        ini_set('session.use_only_cookies', '1');

        $this->configureSidLength();
    }

    /**
     * Configure session ID length
     *
     * @return void
     */
    protected function configureSidLength()
    {
        $bitsPerCharacter = (int)ini_get('session.sid_bits_per_character');
        $sidLength = (int)ini_get('session.sid_length');
        
        if (($bits = $sidLength * $bitsPerCharacter) < 160) {
            $sidLength += (int)ceil((160 % $bits) / $bitsPerCharacter);
            ini_set('session.sid_length', (string)$sidLength);
        }

        switch ($bitsPerCharacter) {
            case 4:
                $this->sidRegexp = '[0-9a-f]';
                break;
            case 5:
                $this->sidRegexp = '[0-9a-v]';
                break;
            case 6:
                $this->sidRegexp = '[0-9a-zA-Z,-]';
                break;
        }
        $this->sidRegexp .= '{' . $sidLength . '}';
    }

    /**
     * Initialize session variables
     *
     * @return void
     */
    protected function initVars()
    {
        if (!empty($_SESSION['__ci_vars'])) {
            $currentTime = time();
            foreach ($_SESSION['__ci_vars'] as $key => &$value) {
                if ($value === 'new') {
                    $_SESSION['__ci_vars'][$key] = 'old';
                } elseif ($value === 'old' || $value < $currentTime) {
                    unset($_SESSION[$key], $_SESSION['__ci_vars'][$key]);
                }
            }

            if (empty($_SESSION['__ci_vars'])) {
                unset($_SESSION['__ci_vars']);
            }
        }

        $this->userdata = &$_SESSION;
    }

    /**
     * Mark keys as flash data
     *
     * @param mixed $key
     * @return bool
     */
    public function markAsFlash($key)
    {
        if (is_array($key)) {
            foreach ($key as $k) {
                if (!isset($_SESSION[$k])) {
                    return false;
                }
            }
            $new = array_fill_keys($key, 'new');
            $_SESSION['__ci_vars'] = isset($_SESSION['__ci_vars'])
                ? array_merge($_SESSION['__ci_vars'], $new)
                : $new;
            return true;
        }

        if (!isset($_SESSION[$key])) {
            return false;
        }

        $_SESSION['__ci_vars'][(string)$key] = 'new';
        return true;
    }

    /**
     * Get flash data keys
     *
     * @return array
     */
    public function getFlashKeys()
    {
        if (!isset($_SESSION['__ci_vars'])) {
            return [];
        }

        $keys = [];
        foreach (array_keys($_SESSION['__ci_vars']) as $key) {
            is_int($_SESSION['__ci_vars'][$key]) or $keys[] = $key;
        }

        return $keys;
    }

    /**
     * Unmark flash data
     *
     * @param mixed $key
     * @return void
     */
    public function unmarkFlash($key)
    {
        if (empty($_SESSION['__ci_vars'])) {
            return;
        }

        is_array($key) or $key = [$key];
        foreach ($key as $k) {
            if (isset($_SESSION['__ci_vars'][$k]) && !is_int($_SESSION['__ci_vars'][$k])) {
                unset($_SESSION['__ci_vars'][$k]);
            }
        }

        if (empty($_SESSION['__ci_vars'])) {
            unset($_SESSION['__ci_vars']);
        }
    }

    /**
     * Mark keys as temp data
     *
     * @param mixed $key
     * @param int $ttl
     * @return bool
     */
    public function markAsTemp($key, $ttl = 300)
    {
        $expire = $ttl + time();
        if (is_array($key)) {
            $temp = [];
            foreach ($key as $k => $v) {
                if (is_int($k)) {
                    $k = $v;
                    $v = $expire;
                } else {
                    $v += time();
                }

                if (!isset($_SESSION[$k])) {
                    return false;
                }

                $temp[$k] = $v;
            }

            $_SESSION['__ci_vars'] = isset($_SESSION['__ci_vars'])
                ? array_merge($_SESSION['__ci_vars'], $temp)
                : $temp;
            return true;
        }

        if (!isset($_SESSION[$key])) {
            return false;
        }

        $_SESSION['__ci_vars'][(string)$key] = $expire;
        return true;
    }

    /**
     * Get temp data keys
     *
     * @return array
     */
    public function getTempKeys()
    {
        if (!isset($_SESSION['__ci_vars'])) {
            return [];
        }

        $keys = [];
        foreach (array_keys($_SESSION['__ci_vars']) as $key) {
            is_int($_SESSION['__ci_vars'][$key]) && $keys[] = $key;
        }

        return $keys;
    }

    /**
     * Unmark temp data
     *
     * @param mixed $key
     * @return void
     */
    public function unmarkTemp($key)
    {
        if (empty($_SESSION['__ci_vars'])) {
            return;
        }

        is_array($key) or $key = [$key];
        foreach ($key as $k) {
            if (isset($_SESSION['__ci_vars'][$k]) && is_int($_SESSION['__ci_vars'][$k])) {
                unset($_SESSION['__ci_vars'][$k]);
            }
        }

        if (empty($_SESSION['__ci_vars'])) {
            unset($_SESSION['__ci_vars']);
        }
    }

    /**
     * __get magic
     */
    public function __get($key)
    {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        } elseif ($key === 'session_id') {
            return session_id();
        }

        return null;
    }

    /**
     * __isset magic
     */
    public function __isset($key)
    {
        if ($key === 'session_id') {
            return (session_status() === PHP_SESSION_ACTIVE);
        }

        return isset($_SESSION[$key]);
    }

    /**
     * __set magic
     */
    public function __set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Destroy session
     */
    public function sessDestroy()
    {
        session_destroy();
    }

    /**
     * Regenerate session ID
     *
     * @param bool $destroy
     */
    public function sessRegenerate($destroy = false)
    {
        $_SESSION['__ci_last_regenerate'] = time();
        session_regenerate_id($destroy);
    }

    /**
     * Get user data reference
     *
     * @return array
     */
    public function &getUserdata()
    {
        return $_SESSION;
    }

    /**
     * Get user data
     *
     * @param string|null $key
     * @return mixed
     */
    public function userdata($key = null)
    {
        if (isset($key)) {
            return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
        } elseif (empty($_SESSION)) {
            return [];
        }

        $userdata = [];
        $exclude = array_merge(
            ['__ci_vars'],
            $this->getFlashKeys(),
            $this->getTempKeys()
        );

        foreach (array_keys($_SESSION) as $k) {
            if (!in_array($k, $exclude, true)) {
                $userdata[$k] = $_SESSION[$k];
            }
        }

        return $userdata;
    }

    /**
     * Set user data
     *
     * @param mixed $data
     * @param mixed $value
     */
    public function setUserdata($data, $value = null)
    {
        if (is_array($data)) {
            foreach ($data as $key => &$val) {
                $_SESSION[$key] = $val;
            }
            return;
        }

        $_SESSION[$data] = $value;
    }

    /**
     * Unset user data
     *
     * @param mixed $key
     */
    public function unsetUserdata($key)
    {
        if (is_array($key)) {
            foreach ($key as $k) {
                unset($_SESSION[$k]);
            }
            return;
        }

        unset($_SESSION[$key]);
    }

    /**
     * All user data alias
     */
    public function allUserdata()
    {
        return $this->userdata();
    }

    /**
     * Check if user data exists
     */
    public function hasUserdata($key)
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Get flash data
     *
     * @param string|null $key
     * @return mixed
     */
    public function flashdata($key = null)
    {
        if (isset($key)) {
            return (isset($_SESSION['__ci_vars'], $_SESSION['__ci_vars'][$key], $_SESSION[$key]) && !is_int($_SESSION['__ci_vars'][$key]))
                ? $_SESSION[$key]
                : null;
        }

        $flashdata = [];
        if (!empty($_SESSION['__ci_vars'])) {
            foreach ($_SESSION['__ci_vars'] as $k => &$v) {
                is_int($v) or $flashdata[$k] = $_SESSION[$k];
            }
        }

        return $flashdata;
    }

    /**
     * Set flash data
     */
    public function setFlashdata($data, $value = null)
    {
        $this->setUserdata($data, $value);
        $this->markAsFlash(is_array($data) ? array_keys($data) : $data);
    }

    /**
     * Keep flash data
     */
    public function keepFlashdata($key)
    {
        $this->markAsFlash($key);
    }

    /**
     * Get temp data
     *
     * @param string|null $key
     * @return mixed
     */
    public function tempdata($key = null)
    {
        if (isset($key)) {
            return (isset($_SESSION['__ci_vars'], $_SESSION['__ci_vars'][$key], $_SESSION[$key]) && is_int($_SESSION['__ci_vars'][$key]))
                ? $_SESSION[$key]
                : null;
        }

        $tempdata = [];
        if (!empty($_SESSION['__ci_vars'])) {
            foreach ($_SESSION['__ci_vars'] as $k => &$v) {
                is_int($v) && $tempdata[$k] = $_SESSION[$k];
            }
        }

        return $tempdata;
    }

    /**
     * Set temp data
     */
    public function setTempdata($data, $value = null, $ttl = 300)
    {
        $this->setUserdata($data, $value);
        $this->markAsTemp(is_array($data) ? array_keys($data) : $data, $ttl);
    }

    /**
     * Unset temp data
     */
    public function unsetTempdata($key)
    {
        $this->unmarkTemp($key);
    }
}
