<?php namespace Root\Services;

use Root\Core\Registry;

class Auth
{
    protected $session;
    protected $config;

    public function __construct()
    {
        $this->session = &Registry::getInstance('Session');
        $this->config = &Registry::getInstance('Config');
    }

    public function login($username, $password)
    {
        $rootUser = $_ENV['ROOT_USER'] ?? null;
        $rootPass = $_ENV['ROOT_PASS'] ?? null;

        if ($username === $rootUser && $password === $rootPass) {
            $sessionId = session_id();
            
            // Store current session ID to enforce single session
            $storageDir = $this->config->getRootDir() . '/storage/session/root';
            if (!is_dir($storageDir)) mkdir($storageDir, 0755, true);
            file_put_contents($storageDir . '/current_session.id', $sessionId);

            $this->session->setUserdata([
                'logged_in' => true,
                'username'  => $username,
                'is_root'  => true,
                'session_id' => $sessionId,
                'auth_hash' => md5($rootUser . $rootPass) // Store hash of credentials
            ]);
            return true;
        }

        return false;
    }

    public function isLoggedIn()
    {
        if ($this->session->userdata('logged_in') !== true) {
            return false;
        }

        // Check if this session is still the active one
        $storedSessionId = $this->session->userdata('session_id');
        $storageDir = $this->config->getRootDir() . '/storage/session/root';
        $currentActiveId = file_exists($storageDir . '/current_session.id') 
            ? trim(file_get_contents($storageDir . '/current_session.id')) 
            : null;

        if ($storedSessionId !== $currentActiveId) {
            $this->logout();
            return false;
        }

        // Check if credentials have changed (e.g. .env re-generated)
        $rootUser = $_ENV['ROOT_USER'] ?? '';
        $rootPass = $_ENV['ROOT_PASS'] ?? '';
        if ($this->session->userdata('auth_hash') !== md5($rootUser . $rootPass)) {
            $this->logout();
            return false;
        }

        return true;
    }

    public function logout()
    {
        $this->session->sessDestroy();
    }

    public function getUser()
    {
        return $this->session->userdata('username');
    }
}
