<?php namespace Root\Core;
/**
 * Utf8 Class
**/
class Utf8
{
    /**
     * Constructor
     */
    public function __construct()
    {
        if (
            defined('MB_ENABLED') && MB_ENABLED === TRUE
            && ( ! (bool) ini_get('mbstring.func_overload')) // mbstring.func_overload is deprecated in 7.2 and removed in 8.0
        )
        {
            @ini_set('mbstring.internal_encoding', 'UTF-8');
            mb_substitute_character('none');
        }


    }

    /**
     * Clean UTF-8 strings
     */
    public function cleanString($str)
    {
        if ($this->isAscii($str))
        {
            return $str;
        }

        return $this->convertToUtf8($str);
    }

    /**
     * Is Ascii?
     */
    public function isAscii($str)
    {
        return (preg_match('/[^\x00-\x7F]/', (string)$str) === 0);
    }

    /**
     * Convert to UTF-8
     */
    public function convertToUtf8($str, $encoding = 'UTF-8')
    {
        if (MB_ENABLED)
        {
            return mb_convert_encoding((string)$str, 'UTF-8', $encoding);
        }

        return (string)$str;
    }
}
