<?php namespace Admin\Core;

/**
 * Security Class
 */
#[\AllowDynamicProperties]
class Security
{
    /**
     * List of bad characters for filenames
     *
     * @var array
     */
    public $filenameBadChars = [
        '../', '<!--', '-->', '<', '>',
        "'", '"', '&', '$', '#',
        '{', '}', '[', ']', '=',
        ';', '?', '%20', '%22',
        '%3c',		// <
        '%253c',	// <
        '%3e',		// >
        '%0e',		// >
        '%28',		// (
        '%29',		// )
        '%2528',	// (
        '%26',		// &
        '%24',		// $
        '%3f',		// ?
        '%3b',		// ;
        '%3d'		// =
    ];

    /**
     * Character set
     *
     * @var string
     */
    public $charset = 'UTF-8';

    /**
     * XSS Hash
     *
     * @var string
     */
    protected $xssHash;

    /**
     * CSRF Hash
     *
     * @var string
     */
    protected $csrfHash;

    /**
     * CSRF Expiration time
     *
     * @var int
     */
    protected $csrfExpire = 7200;

    /**
     * CSRF Token name
     *
     * @var string
     */
    protected $csrfTokenName = 'ci_csrf_token';

    /**
     * CSRF Cookie name
     *
     * @var string
     */
    protected $csrfCookieName = 'ci_csrf_token';

    /**
     * List of never allowed strings
     *
     * @var array
     */
    protected $neverAllowedStr = [
        'document.cookie' => '[removed]',
        '(document).cookie' => '[removed]',
        'document.write'  => '[removed]',
        '(document).write'  => '[removed]',
        '.parentNode'     => '[removed]',
        '.innerHTML'      => '[removed]',
        '-moz-binding'    => '[removed]',
        '<!--'            => '&lt;!--',
        '-->'             => '--&gt;',
        '<![CDATA['       => '&lt;![CDATA[',
        '<comment>'	  => '&lt;comment&gt;',
        '<%'              => '&lt;&#37;'
    ];

    /**
     * List of never allowed regex patterns
     *
     * @var array
     */
    protected $neverAllowedRegex = [
        'javascript\s*:',
        '(\(?document\)?|\(?window\)?(\.document)?)\.(location|on\w*)',
        'expression\s*(\(|&\#40;)', // CSS and IE
        'vbscript\s*:', // IE, surprise!
        'wscript\s*:', // IE
        'jscript\s*:', // IE
        'vbs\s*:', // IE
        'Redirect\s+30\d',
        "([\"'])?data\s*:[^\\1]*?base64[^\\1]*?,[^\\1]*?\\1?"
    ];

    /**
     * Constructor
     */
    public function __construct()
    {
        if (Common::configItem('csrf_protection')) {
            foreach (['csrf_expire', 'csrf_token_name', 'csrf_cookie_name'] as $key) {
                if (null !== ($val = Common::configItem($key))) {
                    $camelKey = 'csrf' . ucfirst(substr($key, 5));
                    $this->$camelKey = $val;
                }
            }

            if ($cookiePrefix = Common::configItem('cookie_prefix')) {
                $this->csrfCookieName = $cookiePrefix . $this->csrfCookieName;
            }

            $this->csrfSetHash();
        }

        $this->charset = strtoupper((string)Common::configItem('charset'));
        Error::logMessage('info', 'Security Class Initialized');
    }

    /**
     * CSRF Verify
     *
     * @return	Security
     */
    public function csrfVerify()
    {
        if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST') {
            return $this->csrfSetCookie();
        }

        if ($excludeUris = Common::configItem('csrf_exclude_uris')) {
            $request = \Admin\Core\Request\Request::createFromGlobals();
            $pathInfo = ltrim($request->getPathInfo(), '/');

            foreach ($excludeUris as $excluded) {
                if (preg_match('#^' . $excluded . '$#i' . (UTF8_ENABLED ? 'u' : ''), $pathInfo)) {
                    return $this;
                }
            }
        }

        $valid = isset($_POST[$this->csrfTokenName], $_COOKIE[$this->csrfCookieName])
            && is_string($_POST[$this->csrfTokenName]) && is_string($_COOKIE[$this->csrfCookieName])
            && hash_equals($_POST[$this->csrfTokenName], $_COOKIE[$this->csrfCookieName]);

        unset($_POST[$this->csrfTokenName]);

        if (Common::configItem('csrf_regenerate')) {
            unset($_COOKIE[$this->csrfCookieName]);
            $this->csrfHash = null;
        }

        $this->csrfSetHash();
        $this->csrfSetCookie();

        if ($valid !== true) {
            $this->csrfShowError();
        }

        Error::logMessage('info', 'CSRF token verified');
        return $this;
    }

    /**
     * CSRF Set Cookie
     *
     * @return	Security|bool
     */
    public function csrfSetCookie()
    {
        $expire = time() + $this->csrfExpire;
        $secureCookie = (bool)Common::configItem('cookie_secure');

        if ($secureCookie && !Common::isHttps()) {
            return false;
        }

        if (Common::isPhp('7.3')) {
            setcookie(
                $this->csrfCookieName,
                $this->csrfHash,
                [
                    'expires'  => $expire,
                    'path'     => Common::configItem('cookie_path'),
                    'domain'   => Common::configItem('cookie_domain'),
                    'secure'   => $secureCookie,
                    'httponly' => Common::configItem('cookie_httponly'),
                    'samesite' => 'Strict'
                ]
            );
        } else {
            $domain = trim((string)Common::configItem('cookie_domain'));
            header('Set-Cookie: ' . $this->csrfCookieName . '=' . $this->csrfHash
                    . '; Expires=' . gmdate('D, d-M-Y H:i:s T', $expire)
                    . '; Max-Age=' . $this->csrfExpire
                    . '; Path=' . rawurlencode((string)Common::configItem('cookie_path'))
                    . ($domain === '' ? '' : '; Domain=' . $domain)
                    . ($secureCookie ? '; Secure' : '')
                    . (Common::configItem('cookie_httponly') ? '; HttpOnly' : '')
                    . '; SameSite=Strict'
            );
        }

        Error::logMessage('info', 'CSRF cookie sent');
        return $this;
    }

    /**
     * Show CSRF Error
     *
     * @return	void
     */
    public function csrfShowError()
    {
        Error::showError('The action you have requested is not allowed.', 403);
    }

    /**
     * Get CSRF Hash
     *
     * @return	string
     */
    public function getCsrfHash()
    {
        return $this->csrfHash;
    }

    /**
     * Get CSRF Token Name
     *
     * @return	string
     */
    public function getCsrfTokenName()
    {
        return $this->csrfTokenName;
    }

    /**
     * XSS Clean
     *
     * @param	mixed	$str
     * @param	bool	$isImage
     * @return	mixed
     */
    public function xssClean($str, $isImage = false)
    {
        if (is_array($str)) {
            foreach ($str as $key => &$value) {
                $str[$key] = $this->xssClean($value);
            }
            return $str;
        }

        $str = Common::removeInvisibleCharacters($str);

        if (stripos((string)$str, '%') !== false) {
            do {
                $oldstr = $str;
                $str = rawurldecode((string)$str);
                $str = preg_replace_callback('#%(?:\s*[0-9a-f]){2,}#i', [$this, 'urlDecodeSpaces'], (string)$str);
            } while ($oldstr !== $str);
            unset($oldstr);
        }

        $str = preg_replace_callback("/[^a-z0-9>]+[a-z0-9]+=([\'\"]).*?\\1/si", [$this, 'convertAttribute'], (string)$str);
        $str = preg_replace_callback('/<\w+.*/si', [$this, 'decodeEntity'], (string)$str);
        $str = Common::removeInvisibleCharacters($str);
        $str = str_replace("\t", ' ', (string)$str);
        $convertedString = $str;
        $str = $this->doNeverAllowed((string)$str);

        if ($isImage === true) {
            $str = preg_replace('/<\?(php)/i', '&lt;?\\1', $str);
        } else {
            $str = str_replace(['<?', '?'.'>'], ['&lt;?', '?&gt;'], $str);
        }

        $words = [
            'javascript', 'expression', 'vbscript', 'jscript', 'wscript',
            'vbs', 'script', 'base64', 'applet', 'alert', 'document',
            'write', 'cookie', 'window', 'confirm', 'prompt', 'eval'
        ];

        foreach ($words as $word) {
            $word = implode('\s*', str_split($word)) . '\s*';
            $str = preg_replace_callback('#(' . substr($word, 0, -3) . ')(\W)#is', [$this, 'compactExplodedWords'], $str);
        }

        do {
            $original = $str;
            if (preg_match('/<a/i', $str)) {
                $str = preg_replace_callback('#<a(?:rea)?[^a-z0-9>]+([^>]*?)(?:>|$)#si', [$this, 'jsLinkRemoval'], $str);
            }
            if (preg_match('/<img/i', $str)) {
                $str = preg_replace_callback('#<img[^a-z0-9]+([^>]*?)(?:\s?/?>|$)#si', [$this, 'jsImgRemoval'], $str);
            }
            if (preg_match('/script|xss/i', $str)) {
                $str = preg_replace('#</*(?:script|xss).*?>#si', '[removed]', $str);
            }
        } while ($original !== $str);

        unset($original);

        $pattern = '#'
            . '<((?<slash>/*\s*)((?<tagName>[a-z0-9]+)(?=[^a-z0-9]|$)|.+)' // tag start and name, followed by a non-tag character
            . '[^\s\042\047a-z0-9>/=]*' // a valid attribute character immediately after the tag would count as a separator
            . '(?<attributes>(?:[\s\042\047/=]*' // non-attribute characters, excluding > (tag close) for obvious reasons
            . '[^\s\042\047>/=]+' // attribute characters
                . '(?:\s*=' // attribute-value separator
                    . '(?:[^\s\042\047=><`]+|\s*\042[^\042]*\042|\s*\047[^\047]*\047|\s*(?U:[^\s\042\047=><`]*))' // single, double or non-quoted value
                . ')?' // end optional attribute-value group
            . ')*)' // end optional attributes group
            . '[^>]*)(?<closeTag>\>)?#isS';

        do {
            $oldStr = $str;
            $str = preg_replace_callback($pattern, [$this, 'sanitizeNaughtyHtml'], (string)$str);
        } while ($oldStr !== $str);

        unset($oldStr);

        $str = preg_replace(
            '#(alert|prompt|confirm|cmd|passthru|eval|exec|expression|system|fopen|fsockopen|file|file_get_contents|readfile|unlink)(\s*)\((.*?)\)#si',
            '\\1\\2&#40;\\3&#41;',
            $str
        );

        $str = preg_replace(
            '#(alert|prompt|confirm|cmd|passthru|eval|exec|expression|system|fopen|fsockopen|file|file_get_contents|readfile|unlink)(\s*)`(.*?)`#si',
            '\\1\\2&#96;\\3&#96;',
            $str
        );

        $str = $this->doNeverAllowed($str);

        if ($isImage === true) {
            return ($str === $convertedString);
        }

        return $str;
    }

    /**
     * XSS Hash
     *
     * @return	string
     */
    public function xssHash()
    {
        if ($this->xssHash === null) {
            $rand = $this->getRandomBytes(16);
            $this->xssHash = ($rand === false)
                ? md5(uniqid((string)mt_rand(), true))
                : bin2hex($rand);
        }
        return $this->xssHash;
    }

    /**
     * Get random bytes
     *
     * @param	int	$length
     * @return	string|bool
     */
    public function getRandomBytes($length)
    {
        if (empty($length) || !ctype_digit((string)$length)) {
            return false;
        }

        if (function_exists('random_bytes')) {
            try {
                return random_bytes((int)$length);
            } catch (\Exception $e) {
                Error::logMessage('error', $e->getMessage());
                return false;
            }
        }

        if (defined('MCRYPT_DEV_URANDOM') && ($output = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM)) !== false) {
            return $output;
        }

        if (is_readable('/dev/urandom') && ($fp = fopen('/dev/urandom', 'rb')) !== false) {
            Common::isPhp('5.4') && stream_set_chunk_size($fp, $length);
            $output = fread($fp, $length);
            fclose($fp);
            if ($output !== false) {
                return $output;
            }
        }

        if (function_exists('openssl_random_pseudo_bytes')) {
            return openssl_random_pseudo_bytes($length);
        }

        return false;
    }

    /**
     * Entity decode
     *
     * @param	string	$str
     * @param	string	$charset
     * @return	string
     */
    public function entityDecode($str, $charset = null)
    {
        if (strpos((string)$str, '&') === false) {
            return $str;
        }

        static $entities;
        isset($charset) or $charset = $this->charset;
        $flag = Common::isPhp('5.4') ? ENT_COMPAT | ENT_HTML5 : ENT_COMPAT;

        if (!isset($entities)) {
            $entities = array_map('strtolower', get_html_translation_table(HTML_ENTITIES, $flag, $charset));
            if ($flag === ENT_COMPAT) {
                $entities[':'] = '&colon;';
                $entities['('] = '&lpar;';
                $entities[')'] = '&rpar;';
                $entities["\n"] = '&NewLine;';
                $entities["\t"] = '&Tab;';
            }
        }

        do {
            $strCompare = $str;
            if (preg_match_all('/&[a-z]{2,}(?![a-z;])/i', (string)$str, $matches)) {
                $replace = [];
                $matches = array_unique(array_map('strtolower', $matches[0]));
                foreach ($matches as &$match) {
                    if (($char = array_search($match . ';', $entities, true)) !== false) {
                        $replace[$match] = $char;
                    }
                }
                $str = str_replace(array_keys($replace), array_values($replace), (string)$str);
            }

            $str = html_entityDecode(
                preg_replace('/(&#(?:x0*[0-9a-f]{2,5}(?![0-9a-f;])|(?:0*\d{2,4}(?![0-9;]))))/iS', '$1;', (string)$str),
                $flag,
                $charset
            );

            if ($flag === ENT_COMPAT) {
                $str = str_replace(array_values($entities), array_keys($entities), (string)$str);
            }
        } while ($strCompare !== $str);

        return $str;
    }

    /**
     * Sanitize filename
     *
     * @param	string	$str
     * @param	bool	$relativePath
     * @return	string
     */
    public function sanitizeFilename($str, $relativePath = false)
    {
        $bad = $this->filenameBadChars;
        if (!$relativePath) {
            $bad[] = './';
            $bad[] = '/';
        }

        $str = Common::removeInvisibleCharacters($str, false);
        do {
            $old = $str;
            $str = str_replace($bad, '', (string)$str);
        } while ($old !== $str);

        return stripslashes((string)$str);
    }

    /**
     * Strip image tags
     *
     * @param	string	$str
     * @return	string
     */
    public function stripImageTags($str)
    {
        return preg_replace(
            [
                '#<img[\s/]+.*?src\s*=\s*(["\'])([^\\1]+?)\\1.*?\>#i',
                '#<img[\s/]+.*?src\s*=\s*?(([^\s"\'=<>`]+)).*?\>#i'
            ],
            '\\2',
            (string)$str
        );
    }

    /**
     * URL Decode spaces
     */
    protected function urlDecodeSpaces($matches)
    {
        $input = $matches[0];
        $nospaces = preg_replace('#\s+#', '', $input);
        return ($nospaces === $input) ? $input : rawurldecode($nospaces);
    }

    /**
     * Compact exploded words
     */
    protected function compactExplodedWords($matches)
    {
        return preg_replace('/\s+/s', '', $matches[1]) . $matches[2];
    }

    /**
     * Sanitize naughty HTML
     */
    protected function sanitizeNaughtyHtml($matches)
    {
        static $naughtyTags = [
            'alert', 'area', 'prompt', 'confirm', 'applet', 'audio', 'basefont', 'base', 'behavior', 'bgsound',
            'blink', 'body', 'embed', 'expression', 'form', 'frameset', 'frame', 'head', 'html', 'ilayer',
            'iframe', 'input', 'button', 'select', 'isindex', 'layer', 'link', 'meta', 'keygen', 'object',
            'plaintext', 'style', 'script', 'textarea', 'title', 'math', 'video', 'svg', 'xml', 'xss'
        ];
        static $evilAttributes = [
            'on\w+', 'style', 'xmlns', 'formaction', 'form', 'xlink:href', 'FSCommand', 'seekSegmentTime'
        ];

        if (empty($matches['closeTag'])) {
            return '&lt;' . $matches[1];
        } elseif (in_array(strtolower($matches['tagName']), $naughtyTags, true)) {
            return '&lt;' . $matches[1] . '&gt;';
        } elseif (isset($matches['attributes'])) {
            $attributes = [];
            $attributesPattern = '#'
                . '(?<name>[^\s\042\047>/=]+)' // attribute characters
                . '(?:\s*=(?<value>[^\s\042\047=><`]+|\s*\042[^\042]*\042|\s*\047[^\047]*\047|\s*(?U:[^\s\042\047=><`]*)))' // attribute-value separator
                . '#i';
            $isEvilPattern = '#^(' . implode('|', $evilAttributes) . ')$#i';

            do {
                $matches['attributes'] = preg_replace('#^[^a-z]+#i', '', $matches['attributes']);
                if (!preg_match($attributesPattern, $matches['attributes'], $attribute, PREG_OFFSET_CAPTURE)) {
                    break;
                }
                if (preg_match($isEvilPattern, $attribute['name'][0]) || (trim($attribute['value'][0]) === '')) {
                    $attributes[] = 'xss=removed';
                } else {
                    $attributes[] = $attribute[0][0];
                }
                $matches['attributes'] = substr($matches['attributes'], $attribute[0][1] + strlen($attribute[0][0]));
            } while ($matches['attributes'] !== '');

            $attributes = empty($attributes) ? '' : ' ' . implode(' ', $attributes);
            return '<' . $matches['slash'] . $matches['tagName'] . $attributes . '>';
        }

        return $matches[0];
    }

    /**
     * JS link removal
     */
    protected function jsLinkRemoval($match)
    {
        return str_replace(
            $match[1],
            preg_replace(
                '#href=.*?(?:(?:alert|prompt|confirm)(?:\(|&\#40;|`|&\#96;)|javascript:|livescript:|mocha:|charset=|window\.|\(?document\)?\.|\.cookie|<script|<xss|d\s*a\s*t\s*a\s*:)#si',
                '',
                $this->filterAttributes($match[1])
            ),
            $match[0]
        );
    }

    /**
     * JS image removal
     */
    protected function jsImgRemoval($match)
    {
        return str_replace(
            $match[1],
            preg_replace(
                '#src=.*?(?:(?:alert|prompt|confirm|eval)(?:\(|&\#40;|`|&\#96;)|javascript:|livescript:|mocha:|charset=|window\.|\(?document\)?\.|\.cookie|<script|<xss|base64\s*,)#si',
                '',
                $this->filterAttributes($match[1])
            ),
            $match[0]
        );
    }

    /**
     * Convert attribute
     */
    protected function convertAttribute($match)
    {
        return str_replace(['>', '<', '\\'], ['&gt;', '&lt;', '\\\\'], $match[0]);
    }

    /**
     * Filter attributes
     */
    protected function filterAttributes($str)
    {
        $out = '';
        if (preg_match_all('#\s*[a-z\-]+\s*=\s*(\042|\047)([^\\1]*?)\\1#is', (string)$str, $matches)) {
            foreach ($matches[0] as $match) {
                $out .= preg_replace('#/\*.*?\*/#s', '', $match);
            }
        }
        return $out;
    }

    /**
     * Decode entity
     */
    protected function decodeEntity($match)
    {
        $match = preg_replace('|\&([a-z\_0-9\-]+)\=([a-z\_0-9\-/]+)|i', $this->xssHash() . '\\1=\\2', $match[0]);
        return str_replace(
            $this->xssHash(),
            '&',
            $this->entityDecode($match, $this->charset)
        );
    }

    /**
     * Do never allowed
     */
    protected function doNeverAllowed($str)
    {
        $str = str_replace(array_keys($this->neverAllowedStr), $this->neverAllowedStr, (string)$str);
        foreach ($this->neverAllowedRegex as $regex) {
            $str = preg_replace('#' . $regex . '#is', '[removed]', (string)$str);
        }
        return $str;
    }

    /**
     * CSRF Set Hash
     */
    protected function csrfSetHash()
    {
        if ($this->csrfHash === null) {
            if (isset($_COOKIE[$this->csrfCookieName]) && is_string($_COOKIE[$this->csrfCookieName])
                && preg_match('#^[0-9a-f]{32}$#iS', $_COOKIE[$this->csrfCookieName]) === 1) {
                return $this->csrfHash = $_COOKIE[$this->csrfCookieName];
            }
            $rand = $this->getRandomBytes(16);
            $this->csrfHash = ($rand === false)
                ? md5(uniqid((string)mt_rand(), true))
                : bin2hex($rand);
        }
        return $this->csrfHash;
    }
}
