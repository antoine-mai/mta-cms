<?php namespace Root\Services\Session;

/**
 * Driver Abstract Class
 */
abstract class Driver
{
    /**
     * Configuration array
     *
     * @var array
     */
    protected $config;

    /**
     * Session fingerprint
     *
     * @var string
     */
    protected $fingerprint;

    /**
     * Lock status
     *
     * @var bool
     */
    protected $lock = false;

    /**
     * Session ID
     *
     * @var string
     */
    protected $sessionId;

    /**
     * Success and failure return values
     */
    protected $success = true;
    protected $failure = false;

    /**
     * Constructor
     *
     * @param array $params
     */
    public function __construct(array &$params)
    {
        $this->config = &$params;
    }

    /**
     * Destroy session cookie
     *
     * @return bool
     */
    protected function cookieDestroy()
    {
        return setcookie(
            $this->config['cookie_name'],
            '',
            [
                'expires' => 1,
                'path' => $this->config['cookie_path'],
                'domain' => $this->config['cookie_domain'],
                'secure' => $this->config['cookie_secure'],
                'httponly' => true,
                'samesite' => $this->config['cookie_samesite']
            ]
        );
    }

    /**
     * Get lock
     *
     * @param string $sessionId
     * @return bool
     */
    protected function getLock($sessionId)
    {
        $this->lock = true;
        return true;
    }

    /**
     * Release lock
     *
     * @return bool
     */
    protected function releaseLock()
    {
        if ($this->lock) {
            $this->lock = false;
        }
        return true;
    }
}
