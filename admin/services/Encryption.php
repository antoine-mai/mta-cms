<?php namespace Admin\Services;

/**
 * Encryption Class
 *
 * Provides two-way data encryption and authentication using OpenSSL.
 */
class Encryption
{
    /**
     * Cipher
     *
     * @var string
     */
    protected $cipher = 'aes-128';

    /**
     * Mode
     *
     * @var string
     */
    protected $mode = 'cbc';

    /**
     * Handle (Cipher + Mode)
     *
     * @var string
     */
    protected $handle;

    /**
     * Key
     *
     * @var string
     */
    protected $key;

    /**
     * Supported modes mapping
     *
     * @var array
     */
    protected $modes = [
        'openssl' => [
            'cbc' => 'cbc',
            'ecb' => 'ecb',
            'ofb' => 'ofb',
            'cfb' => 'cfb',
            'cfb8' => 'cfb8',
            'ctr' => 'ctr',
            'stream' => '',
            'xts' => 'xts'
        ]
    ];

    /**
     * Digest sizes mapping
     *
     * @var array
     */
    protected $digests = [
        'sha224' => 28,
        'sha256' => 32,
        'sha384' => 48,
        'sha512' => 64
    ];

    /**
     * Constructor
     *
     * @param	array	$params
     */
    public function __construct(array $params = [])
    {
        if (!extension_loaded('openssl')) {
            \Admin\Core\Error::showError('Encryption: Unable to find an available encryption driver.');
        }

        $this->initialize($params);

        if (!isset($this->key) && ($key = \Admin\Core\Common::configItem('encryption_key'))) {
            $this->key = $key;
        }

        \Admin\Core\Error::logMessage('info', 'Encryption Class Initialized');
    }

    /**
     * Initialize Settings
     *
     * @param	array	$params
     * @return	Encryption
     */
    public function initialize(array $params)
    {
        empty($params['cipher']) && $params['cipher'] = $this->cipher;
        empty($params['key']) or $this->key = $params['key'];

        if (!empty($params['cipher'])) {
            $params['cipher'] = strtolower((string)$params['cipher']);
            $this->cipherAlias($params['cipher']);
            $this->cipher = $params['cipher'];
        }

        if (!empty($params['mode'])) {
            $params['mode'] = strtolower((string)$params['mode']);
            if (isset($this->modes['openssl'][$params['mode']])) {
                $this->mode = $this->modes['openssl'][$params['mode']];
            }
        }

        if (isset($this->cipher, $this->mode)) {
            $handle = empty($this->mode) ? $this->cipher : $this->cipher . '-' . $this->mode;
            if (!in_array($handle, openssl_get_cipher_methods(), true)) {
                $this->handle = null;
                \Admin\Core\Error::logMessage('error', 'Encryption: Unable to initialize OpenSSL with method ' . strtoupper((string)$handle) . '.');
            } else {
                $this->handle = (string)$handle;
            }
        }

        return $this;
    }

    /**
     * Create random key
     *
     * @param	int	$length
     * @return	string
     */
    public function createKey($length)
    {
        return random_bytes((int)$length);
    }

    /**
     * Encrypt data
     *
     * @param	string	$data
     * @param	array|null	$params
     * @return	string|bool
     */
    public function encrypt($data, ?array $params = null)
    {
        if (($params = $this->getParams($params)) === false) {
            return false;
        }

        isset($params['key']) or $params['key'] = $this->hkdf((string)$this->key, 'sha512', null, strlen((string)$this->key), 'encryption');

        if (empty($params['handle'])) {
            return false;
        }

        $iv = ($ivSize = openssl_cipher_iv_length($params['handle'])) ? $this->createKey($ivSize) : '';
        $data = openssl_encrypt((string)$data, $params['handle'], (string)$params['key'], 1, (string)$iv);

        if ($data === false) {
            return false;
        }

        $data = $iv . $data;

        $params['base64'] && $data = base64_encode($data);

        if (isset($params['hmac_digest'])) {
            isset($params['hmac_key']) or $params['hmac_key'] = $this->hkdf((string)$this->key, 'sha512', null, null, 'authentication');
            return hash_hmac($params['hmac_digest'], $data, (string)$params['hmac_key'], !$params['base64']) . $data;
        }

        return $data;
    }

    /**
     * Decrypt data
     *
     * @param	string	$data
     * @param	array|null	$params
     * @return	string|bool
     */
    public function decrypt($data, ?array $params = null)
    {
        if (($params = $this->getParams($params)) === false) {
            return false;
        }

        if (isset($params['hmac_digest'])) {
            $digestSize = ($params['base64']) ? $this->digests[$params['hmac_digest']] * 2 : $this->digests[$params['hmac_digest']];
            if (strlen((string)$data) <= $digestSize) {
                return false;
            }

            $hmacInput = substr((string)$data, 0, $digestSize);
            $data = substr((string)$data, $digestSize);
            isset($params['hmac_key']) or $params['hmac_key'] = $this->hkdf((string)$this->key, 'sha512', null, null, 'authentication');
            $hmacCheck = hash_hmac($params['hmac_digest'], (string)$data, (string)$params['hmac_key'], !$params['base64']);

            if (!hash_equals($hmacInput, $hmacCheck)) {
                return false;
            }
        }

        if ($params['base64']) {
            $data = base64_decode((string)$data);
        }

        isset($params['key']) or $params['key'] = $this->hkdf((string)$this->key, 'sha512', null, strlen((string)$this->key), 'encryption');

        if ($ivSize = openssl_cipher_iv_length($params['handle'])) {
            $iv = substr((string)$data, 0, $ivSize);
            $data = substr((string)$data, $ivSize);
        } else {
            $iv = '';
        }

        return openssl_decrypt((string)$data, $params['handle'], (string)$params['key'], 1, (string)$iv);
    }

    /**
     * Get processing parameters
     *
     * @param	array|null	$params
     * @return	array|bool
     */
    protected function getParams($params)
    {
        if (empty($params)) {
            return isset($this->cipher, $this->mode, $this->key, $this->handle)
                ? [
                    'handle' => $this->handle,
                    'cipher' => $this->cipher,
                    'mode' => $this->mode,
                    'key' => null,
                    'base64' => true,
                    'hmac_digest' => 'sha512',
                    'hmac_key' => null
                ]
                : false;
        }

        $params = [
            'handle' => null,
            'cipher' => isset($params['cipher']) ? $params['cipher'] : $this->cipher,
            'mode' => isset($params['mode']) ? $params['mode'] : $this->mode,
            'key' => isset($params['key']) ? $params['key'] : $this->key,
            'base64' => isset($params['raw_data']) ? !$params['raw_data'] : true,
            'hmac_digest' => isset($params['hmac_digest']) ? $params['hmac_digest'] : 'sha512',
            'hmac_key' => isset($params['hmac_key']) ? $params['hmac_key'] : null
        ];

        $this->cipherAlias($params['cipher']);
        $params['handle'] = ($params['cipher'] !== $this->cipher || $params['mode'] !== $this->mode)
            ? $params['cipher'] . '-' . $params['mode']
            : $this->handle;

        return $params;
    }

    /**
     * Cipher Alias
     *
     * @param	string	$cipher
     * @return	void
     */
    protected function cipherAlias(&$cipher)
    {
        static $dictionary = [
            'rijndael-128' => 'aes-128',
            'tripledes' => 'des-ede3',
            'blowfish' => 'bf',
            'cast-128' => 'cast5',
            'arcfour' => 'rc4-40',
            'rc4' => 'rc4-40'
        ];

        if (isset($dictionary[$cipher])) {
            $cipher = $dictionary[$cipher];
        }
    }

    /**
     * HKDF
     *
     * @param	string	$key
     * @param	string	$digest
     * @param	string	$salt
     * @param	int	$length
     * @param	string	$info
     * @return	string|bool
     */
    public function hkdf($key, $digest = 'sha512', $salt = null, $length = null, $info = '')
    {
        if (!isset($this->digests[$digest])) {
            return false;
        }

        if (empty($length) || !is_int($length)) {
            $length = $this->digests[$digest];
        }

        $salt or $salt = str_repeat("\0", $this->digests[$digest]);

        return hash_hkdf($digest, (string)$key, (int)$length, (string)$info, (string)$salt);
    }

    /**
     * __get magic
     */
    public function __get($key)
    {
        if ($key === 'mode') {
            return array_search($this->mode, $this->modes['openssl'], true);
        } elseif (in_array($key, ['cipher', 'digests'], true)) {
            return $this->{$key};
        }
        return null;
    }
}
