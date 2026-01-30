<?php namespace Root\Services;

/**
 * Utf8 Class
 *
 * Provides UTF-8 support and conversion methods.
 */
class Utf8
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $config = &\Root\Core\Registry::getInstance('Config');
        if (
            defined('PREG_BAD_UTF8_ERROR')
            && (ICONV_ENABLED === true || MB_ENABLED === true)
            && strtoupper((string)($config->item('charset') ?? 'UTF-8')) === 'UTF-8'
        ) {
            if (!defined('UTF8_ENABLED')) {
                define('UTF8_ENABLED', true);
            }
        }
    }

    /**
     * Clean string
     *
     * @param	string	$str
     * @return	string
     */
    public function cleanString($str)
    {
        if ($this->isAscii($str) === false) {
            if (MB_ENABLED) {
                $str = mb_convert_encoding((string)$str, 'UTF-8', 'UTF-8');
            } elseif (ICONV_ENABLED) {
                $str = @iconv('UTF-8', 'UTF-8//IGNORE', (string)$str);
            }
        }
        return (string)$str;
    }

    /**
     * Safe ASCII for XML
     *
     * @param	string	$str
     * @return	string
     */
    public function safeAsciiForXml($str)
    {
        return \Root\Core\Common::removeInvisibleCharacters($str, false);
    }

    /**
     * Convert to UTF-8
     *
     * @param	string	$str
     * @param	string	$encoding
     * @return	string|bool
     */
    public function convertToUtf8($str, $encoding)
    {
        if (MB_ENABLED) {
            return mb_convert_encoding((string)$str, 'UTF-8', (string)$encoding);
        } elseif (ICONV_ENABLED) {
            return @iconv((string)$encoding, 'UTF-8', (string)$str);
        }
        return false;
    }

    /**
     * Is ASCII?
     *
     * @param	string	$str
     * @return	bool
     */
    public function isAscii($str)
    {
        return (preg_match('/[^\x00-\x7F]/S', (string)$str) === 0);
    }
}
