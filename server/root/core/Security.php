<?php namespace Root\Core;
/**
 * Security Class
**/
class Security
{
    /**
     * CSRF cookie name
     */
    protected $csrfTokenName = 'mta_csrf_token';

    /**
     * CSRF hash
     */
    protected $csrfHash;

    /**
     * Constructor
     */
    public function __construct()
    {
        $config = &Registry::getInstance('Config');
        $this->csrfTokenName = $config->item('csrf_token_name') ?: 'mta_csrf_token';

        if (session_status() === PHP_SESSION_ACTIVE) {
            $this->csrfHash = $_SESSION[$this->csrfTokenName] ?? null;
            if (!$this->csrfHash) {
                $this->csrfHash = bin2hex(random_bytes(16));
                $_SESSION[$this->csrfTokenName] = $this->csrfHash;
            }
        }
    }

    /**
     * Get CSRF hash
     */
    public function getCsrfHash()
    {
        return $this->csrfHash;
    }

    /**
     * Get CSRF token name
     */
    public function getCsrfTokenName()
    {
        return $this->csrfTokenName;
    }

    /**
     * Validate CSRF
     */
    public function csrfVerify()
    {
        if (strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            return $this;
        }

        $request = &Registry::getInstance('Request');
        $token = $request->get($this->csrfTokenName);

        if (!$token || $token !== $this->csrfHash) {
             header('HTTP/1.1 403 Forbidden');
             echo json_encode(['error' => 'CSRF verification failed']);
             exit;
        }

        return $this;
    }

    /**
     * XSS Clean
     */
    public function xssClean($str, $isImage = false)
    {
        return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
    }
}
