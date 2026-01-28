<?php namespace Admin\Services;
/**
 * 
**/
class Utf8
{
	public function __construct()
	{
		if (defined('PREG_BAD_UTF8_ERROR') && (ICONV_ENABLED === TRUE OR MB_ENABLED === TRUE) && strtoupper(\Admin\Core\Common::config_item('charset')) === 'UTF-8')
		{
			define('UTF8_ENABLED', TRUE);
			\Admin\Core\Error::log_message('debug', 'UTF-8 Support Enabled');
		}
		else
		{
			define('UTF8_ENABLED', FALSE);
			\Admin\Core\Error::log_message('debug', 'UTF-8 Support Disabled');
		}
		\Admin\Core\Error::log_message('info', 'Utf8 Class Initialized');
	}
	public function cleanString($str)
	{
		if ($this->isAscii($str) === FALSE)
		{
			if (MB_ENABLED)
			{
				$str = mb_convert_encoding($str, 'UTF-8', 'UTF-8');
			}
			elseif (ICONV_ENABLED)
			{
				$str = @iconv('UTF-8', 'UTF-8//IGNORE', $str);
			}
		}
		return $str;
	}
	public function safeAsciiForXml($str)
	{
		return \Admin\Core\Common::remove_invisible_characters($str, FALSE);
	}
	public function convertToUtf8($str, $encoding)
	{
		if (MB_ENABLED)
		{
			return mb_convert_encoding($str, 'UTF-8', $encoding);
		}
		elseif (ICONV_ENABLED)
		{
			return @iconv($encoding, 'UTF-8', $str);
		}
		return FALSE;
	}
	public function isAscii($str)
	{
		return (preg_match('/[^\x00-\x7F]/S', $str) === 0);
	}
}
