<?php namespace Admin\Services;
/**
 * 
**/
class Parser
{
	public $l_delim = '{';
	public $r_delim = '}';
	protected $CI;

	public function __construct()
	{
		$this->CI =& getInstance();
		logMessage('info', 'Parser Class Initialized');
	}

	public function parse($template, $data, $return = false)
	{
		$template = $this->CI->load->view((string)$template, $data, true);
		return $this->_parse((string)$template, $data, $return);
	}

	public function parse_string($template, $data, $return = false)
	{
		return $this->_parse((string)$template, $data, $return);
	}

	protected function _parse($template, $data, $return = false)
	{
		if ($template === '')
		{
			return false;
		}
		$replace = [];
		foreach ($data as $key => $val)
		{
			$replace = array_merge(
				$replace,
				is_array($val)
					? $this->_parse_pair($key, $val, $template)
					: $this->_parse_single($key, (string) $val, $template)
			);
		}
		unset($data);
		$template = strtr((string)$template, $replace);
		if ($return === false)
		{
			$this->CI->output->appendOutput($template);
		}
		return (string)$template;
	}

	public function set_delimiters($l = '{', $r = '}')
	{
		$this->l_delim = $l;
		$this->r_delim = $r;
	}

	protected function _parse_single($key, $val, $string)
	{
		return [$this->l_delim.$key.$this->r_delim => (string) $val];
	}

	protected function _parse_pair($variable, $data, $string)
	{
		$replace = [];
		preg_match_all(
			'#'.preg_quote($this->l_delim.$variable.$this->r_delim).'(.+?)'.preg_quote($this->l_delim.'/'.$variable.$this->r_delim).'#s',
			(string)$string,
			$matches,
			PREG_SET_ORDER
		);
		foreach ($matches as $match)
		{
			$str = '';
			foreach ($data as $row)
			{
				$temp = [];
				foreach ($row as $key => $val)
				{
					if (is_array($val))
					{
						$pair = $this->_parse_pair($key, $val, $match[1]);
						if ( ! empty($pair))
						{
							$temp = array_merge($temp, $pair);
						}
						continue;
					}
					$temp[(string)$this->l_delim.$key.(string)$this->r_delim] = (string)$val;
				}
				$str .= strtr($match[1], $temp);
			}
			$replace[$match[0]] = $str;
		}
		return $replace;
	}
}
