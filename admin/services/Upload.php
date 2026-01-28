<?php namespace Admin\Services;
/**
 * 
**/
use \ReflectionClass;
/**
 * 
**/
class Upload
{
	public $max_size = 0;
	public $max_width = 0;
	public $max_height = 0;
	public $min_width = 0;
	public $min_height = 0;
	public $max_filename = 0;
	public $max_filename_increment = 100;
	public $allowed_types = '';
	public $file_temp = '';
	public $file_name = '';
	public $orig_name = '';
	public $file_type = '';
	public $file_size = null;
	public $file_ext = '';
	public $file_ext_tolower = false;
	public $upload_path = '';
	public $overwrite = false;
	public $encrypt_name = false;
	public $is_image = false;
	public $image_width = null;
	public $image_height = null;
	public $image_type = '';
	public $image_size_str = '';
	public $error_msg = [];
	public $remove_spaces = true;
	public $detect_mime = true;
	public $xssClean = false;
	public $mod_mime_fix = true;
	public $temp_prefix = 'temp_file_';
	public $client_name = '';
	protected $_file_name_override = '';
	protected $_mimes = [];
	protected $_CI;

	public function __construct($config = [])
	{
		empty($config) OR $this->initialize($config, false);
		$this->_mimes =& getMimes();
		$this->_CI =& getInstance();
		logMessage('info', 'Upload Class Initialized');
	}

	public function initialize(array $config = [], $reset = true)
	{
		$reflection = new ReflectionClass($this);
		if ($reset === true)
		{
			$defaults = $reflection->getDefaultProperties();
			foreach (array_keys($defaults) as $key)
			{
				if ($key[0] === '_')
				{
					continue;
				}
				if (isset($config[$key]))
				{
					if ($reflection->hasMethod('set_'.$key))
					{
						$this->{'set_'.$key}($config[$key]);
					}
					else
					{
						$this->$key = $config[$key];
					}
				}
				else
				{
					$this->$key = $defaults[$key];
				}
			}
		}
		else
		{
			foreach ($config as $key => &$value)
			{
				if ($key[0] !== '_' && $reflection->hasProperty($key))
				{
					if ($reflection->hasMethod('set_'.$key))
					{
						$this->{'set_'.$key}($value);
					}
					else
					{
						$this->$key = $value;
					}
				}
			}
		}
		$this->_file_name_override = $this->file_name;
		return $this;
	}

	public function do_upload($field = 'userfile')
	{
		if (isset($_FILES[$field]))
		{
			$_file = $_FILES[$field];
		}
		elseif (($c = preg_match_all('/(?:^[^\[]+)|\[[^]]*\]/', (string)$field, $matches)) > 1)
		{
			$_file = $_FILES;
			for ($i = 0; $i < $c; $i++)
			{
				if (($field_key = trim($matches[0][$i], '[]')) === '' OR ! isset($_file[$field_key]))
				{
					$_file = null;
					break;
				}
				$_file = $_file[$field_key];
			}
		}

		if ( ! isset($_file))
		{
			$this->set_error('upload_no_file_selected', 'debug');
			return false;
		}

		if ( ! $this->validate_upload_path())
		{
			return false;
		}

		if ( ! is_uploaded_file($_file['tmp_name']))
		{
			$error = isset($_file['error']) ? $_file['error'] : 4;
			switch ($error)
			{
				case UPLOAD_ERR_INI_SIZE:
					$this->set_error('upload_file_exceeds_limit', 'info');
					break;
				case UPLOAD_ERR_FORM_SIZE:
					$this->set_error('upload_file_exceeds_form_limit', 'info');
					break;
				case UPLOAD_ERR_PARTIAL:
					$this->set_error('upload_file_partial', 'debug');
					break;
				case UPLOAD_ERR_NO_FILE:
					$this->set_error('upload_no_file_selected', 'debug');
					break;
				case UPLOAD_ERR_NO_TMP_DIR:
					$this->set_error('upload_no_temp_directory', 'error');
					break;
				case UPLOAD_ERR_CANT_WRITE:
					$this->set_error('upload_unable_to_write_file', 'error');
					break;
				case UPLOAD_ERR_EXTENSION:
					$this->set_error('upload_stopped_by_extension', 'debug');
					break;
				default:
					$this->set_error('upload_no_file_selected', 'debug');
					break;
			}
			return false;
		}

		$this->file_temp = $_file['tmp_name'];
		$this->file_size = $_file['size'];
		if ($this->detect_mime !== false)
		{
			$this->_file_mime_type($_file);
		}
		$this->file_type = preg_replace('/^(.+?);.*$/', '\\1', (string)$this->file_type);
		$this->file_type = strtolower(trim(stripslashes((string)$this->file_type), '"'));
		$this->file_name = $this->_prep_filename($_file['name']);
		$this->file_ext	 = $this->get_extension($this->file_name);
		$this->client_name = $this->file_name;

		if ( ! $this->is_allowed_filetype())
		{
			$this->set_error('upload_invalid_filetype', 'debug');
			return false;
		}

		if ($this->_file_name_override !== '')
		{
			$this->file_name = $this->_prep_filename($this->_file_name_override);
			if (strpos((string)$this->_file_name_override, '.') === false)
			{
				$this->file_name .= (string)$this->file_ext;
			}
			else
			{
				$this->file_ext	= (string)$this->get_extension($this->_file_name_override);
			}

			if ( ! $this->is_allowed_filetype(true))
			{
				$this->set_error('upload_invalid_filetype', 'debug');
				return false;
			}
		}

		if ($this->file_size > 0)
		{
			$this->file_size = round((float)$this->file_size/1024, 2);
		}

		if ( ! $this->is_allowed_filesize())
		{
			$this->set_error('upload_invalid_filesize', 'info');
			return false;
		}

		if ( ! $this->is_allowed_dimensions())
		{
			$this->set_error('upload_invalid_dimensions', 'info');
			return false;
		}

		$this->file_name = $this->_CI->security->sanitizeFilename((string)$this->file_name);

		if ($this->max_filename > 0)
		{
			$this->file_name = $this->limit_filename_length((string)$this->file_name, $this->max_filename);
		}

		if ($this->remove_spaces === true)
		{
			$this->file_name = preg_replace('/\s+/', '_', (string)$this->file_name);
		}

		if ($this->file_ext_tolower && ($ext_length = strlen((string)$this->file_ext)))
		{
			$this->file_name = substr((string)$this->file_name, 0, -$ext_length).(string)$this->file_ext;
		}

		$this->orig_name = $this->file_name;

		if (false === ($this->file_name = $this->set_filename((string)$this->upload_path, (string)$this->file_name)))
		{
			return false;
		}

		if ($this->xssClean && $this->do_xssClean() === false)
		{
			$this->set_error('upload_unable_to_write_file', 'error');
			return false;
		}

		if ( ! @copy((string)$this->file_temp, (string)$this->upload_path.(string)$this->file_name))
		{
			if ( ! @move_uploaded_file((string)$this->file_temp, (string)$this->upload_path.(string)$this->file_name))
			{
				$this->set_error('upload_destination_error', 'error');
				return false;
			}
		}

		$this->set_image_properties((string)$this->upload_path.(string)$this->file_name);
		return true;
	}

	public function data($index = null)
	{
		$data = [
				'file_name'		=> $this->file_name,
				'file_type'		=> $this->file_type,
				'file_path'		=> $this->upload_path,
				'full_path'		=> $this->upload_path.$this->file_name,
				'raw_name'		=> substr((string)$this->file_name, 0, -strlen((string)$this->file_ext)),
				'orig_name'		=> $this->orig_name,
				'client_name'		=> $this->client_name,
				'file_ext'		=> $this->file_ext,
				'file_size'		=> $this->file_size,
				'is_image'		=> $this->is_image(),
				'image_width'		=> $this->image_width,
				'image_height'		=> $this->image_height,
				'image_type'		=> $this->image_type,
				'image_size_str'	=> $this->image_size_str,
			];

		if ( ! empty($index))
		{
			return isset($data[$index]) ? $data[$index] : null;
		}

		return $data;
	}

	public function set_upload_path($path)
	{
		$this->upload_path = rtrim((string)$path, '/').'/';
		return $this;
	}

	public function set_filename($path, $filename)
	{
		if ($this->encrypt_name === true)
		{
			$filename = md5(uniqid((string)random_int(0, PHP_INT_MAX))).$this->file_ext;
		}

		if ($this->overwrite === true OR ! file_exists((string)$path.$filename))
		{
			return $filename;
		}

		$filename = str_replace((string)$this->file_ext, '', (string)$filename);
		$new_filename = '';
		for ($i = 1; $i < $this->max_filename_increment; $i++)
		{
			if ( ! file_exists((string)$path.$filename.$i.(string)$this->file_ext))
			{
				$new_filename = $filename.$i.(string)$this->file_ext;
				break;
			}
		}

		if ($new_filename === '')
		{
			$this->set_error('upload_bad_filename', 'debug');
			return false;
		}

		return $new_filename;
	}

	public function set_max_filesize($n)
	{
		$this->max_size = ($n < 0) ? 0 : (int) $n;
		return $this;
	}

	protected function set_max_size($n)
	{
		return $this->set_max_filesize($n);
	}

	public function set_max_filename($n)
	{
		$this->max_filename = ($n < 0) ? 0 : (int) $n;
		return $this;
	}

	public function set_max_width($n)
	{
		$this->max_width = ($n < 0) ? 0 : (int) $n;
		return $this;
	}

	public function set_max_height($n)
	{
		$this->max_height = ($n < 0) ? 0 : (int) $n;
		return $this;
	}

	public function set_min_width($n)
	{
		$this->min_width = ($n < 0) ? 0 : (int) $n;
		return $this;
	}

	public function set_min_height($n)
	{
		$this->min_height = ($n < 0) ? 0 : (int) $n;
		return $this;
	}

	public function set_allowed_types($types)
	{
		$this->allowed_types = (is_array($types) OR $types === '*')
			? $types
			: explode('|', (string)$types);
		return $this;
	}

	public function set_image_properties($path = '')
	{
		if ($this->is_image() && function_exists('getimagesize'))
		{
			if (false !== ($D = @getimagesize((string)$path)))
			{
				$types = [1 => 'gif', 2 => 'jpeg', 3 => 'png'];
				$this->image_width	= $D[0];
				$this->image_height	= $D[1];
				$this->image_type	= isset($types[$D[2]]) ? $types[$D[2]] : 'unknown';
				$this->image_size_str	= $D[3]; // string containing height and width
			}
		}
		return $this;
	}

	public function set_xssClean($flag = false)
	{
		$this->xssClean = ($flag === true);
		return $this;
	}

	public function is_image()
	{
		$png_mimes  = ['image/x-png'];
		$jpeg_mimes = ['image/jpg', 'image/jpe', 'image/jpeg', 'image/pjpeg'];

		if (in_array($this->file_type, $png_mimes))
		{
			$this->file_type = 'image/png';
		}
		elseif (in_array($this->file_type, $jpeg_mimes))
		{
			$this->file_type = 'image/jpeg';
		}

		$img_mimes = ['image/gif',	'image/jpeg', 'image/png', 'image/webp'];
		return in_array($this->file_type, $img_mimes, true);
	}

	public function is_allowed_filetype($ignore_mime = false)
	{
		if ($this->allowed_types === '*')
		{
			return true;
		}

		if (empty($this->allowed_types) OR ! is_array($this->allowed_types))
		{
			$this->set_error('upload_no_file_types', 'debug');
			return false;
		}

		$ext = strtolower(ltrim((string)$this->file_ext, '.'));

		if ( ! in_array($ext, $this->allowed_types, true))
		{
			return false;
		}

		if (in_array($ext, ['gif', 'jpg', 'jpeg', 'jpe', 'png', 'webp'], true) && @getimagesize((string)$this->file_temp) === false)
		{
			return false;
		}

		if ($ignore_mime === true)
		{
			return true;
		}

		if (isset($this->_mimes[$ext]))
		{
			return is_array($this->_mimes[$ext])
				? in_array($this->file_type, $this->_mimes[$ext], true)
				: ($this->_mimes[$ext] === $this->file_type);
		}

		return false;
	}

	public function is_allowed_filesize()
	{
		return ($this->max_size == 0 OR (float)$this->max_size > (float)$this->file_size);
	}

	public function is_allowed_dimensions()
	{
		if ( ! $this->is_image())
		{
			return true;
		}

		if (function_exists('getimagesize'))
		{
			$D = @getimagesize((string)$this->file_temp);
			if ($this->max_width > 0 && $D[0] > $this->max_width)
			{
				return false;
			}
			if ($this->max_height > 0 && $D[1] > $this->max_height)
			{
				return false;
			}
			if ($this->min_width > 0 && $D[0] < $this->min_width)
			{
				return false;
			}
			if ($this->min_height > 0 && $D[1] < $this->min_height)
			{
				return false;
			}
		}

		return true;
	}

	public function validate_upload_path()
	{
		if ($this->upload_path === '')
		{
			$this->set_error('upload_no_filepath', 'error');
			return false;
		}

		if (realpath((string)$this->upload_path) !== false)
		{
			$this->upload_path = str_replace('\\', '/', (string)realpath((string)$this->upload_path));
		}

		if ( ! is_dir((string)$this->upload_path))
		{
			$this->set_error('upload_no_filepath', 'error');
			return false;
		}

		if ( ! isReallyWritable((string)$this->upload_path))
		{
			$this->set_error('upload_not_writable', 'error');
			return false;
		}

		$this->upload_path = preg_replace('/(.+?)\/*$/', '\\1/', (string)$this->upload_path);
		return true;
	}

	public function get_extension($filename)
	{
		$x = explode('.', (string)$filename);
		if (count($x) === 1)
		{
			return '';
		}

		$ext = ($this->file_ext_tolower) ? strtolower(end($x)) : end($x);
		return '.'.$ext;
	}

	public function limit_filename_length($filename, $length)
	{
		if (strlen((string)$filename) < $length)
		{
			return $filename;
		}

		$ext = '';
		if (strpos((string)$filename, '.') !== false)
		{
			$parts		= explode('.', (string)$filename);
			$ext		= '.'.array_pop($parts);
			$filename	= implode('.', $parts);
		}

		return substr((string)$filename, 0, (int)($length - strlen($ext))).$ext;
	}

	public function do_xssClean()
	{
		$file = $this->file_temp;
		if (filesize((string)$file) == 0)
		{
			return false;
		}

		if (memory_get_usage() && ($memory_limit = ini_get('memory_limit')) > 0)
		{
			$memory_limit_parts = str_split($memory_limit, strspn($memory_limit, '1234567890'));
			$memory_limit_val = (float)$memory_limit_parts[0];
			if ( ! empty($memory_limit_parts[1]))
			{
				switch (strtolower($memory_limit_parts[1][0]))
				{
					case 'g':
						$memory_limit_val *= 1024 * 1024 * 1024;
						break;
					case 'm':
						$memory_limit_val *= 1024 * 1024;
						break;
					case 'k':
						$memory_limit_val *= 1024;
						break;
				}
			}
			$new_memory_limit = (int) ceil(filesize((string)$file) + $memory_limit_val);
			ini_set('memory_limit', (string)$new_memory_limit);
		}

		if (function_exists('getimagesize') && @getimagesize((string)$file) !== false)
		{
			if (($fp = @fopen((string)$file, 'rb')) === false)
			{
				return false;
			}
			$opening_bytes = fread($fp, 256);
			fclose($fp);
			return ! preg_match('/<(a|body|head|html|img|plaintext|pre|script|table|title)[\s>]/i', (string)$opening_bytes);
		}

		if (($data = @file_get_contents((string)$file)) === false)
		{
			return false;
		}

		return $this->_CI->security->xssClean($data, true);
	}

	public function set_error($msg, $log_level = 'error')
	{
		$this->_CI->lang->load('upload');
		is_array($msg) OR $msg = [$msg];
		foreach ($msg as $val)
		{
			$msg_text = ($this->_CI->lang->line($val) === false) ? $val : $this->_CI->lang->line($val);
			$this->error_msg[] = $msg_text;
			logMessage($log_level, $msg_text);
		}
		return $this;
	}

	public function display_errors($open = '<p>', $close = '</p>')
	{
		return (count($this->error_msg) > 0) ? $open.implode($close.$open, $this->error_msg).$close : '';
	}

	protected function _prep_filename($filename)
	{
		if ($this->mod_mime_fix === false OR $this->allowed_types === '*' OR ($ext_pos = strrpos((string)$filename, '.')) === false)
		{
			return $filename;
		}

		$ext = substr((string)$filename, $ext_pos);
		$filename = substr((string)$filename, 0, $ext_pos);
		return str_replace('.', '_', $filename).$ext;
	}

	protected function _file_mime_type($file)
	{
		$regexp = '/^([a-z\-]+\/[a-z0-9\-\.\+]+)(;\s.+)?$/';
		if (function_exists('finfo_file'))
		{
			$finfo = @finfo_open(FILEINFO_MIME);
			if ($finfo !== false)
			{
				$mime = @finfo_file($finfo, $file['tmp_name']);
				finfo_close($finfo);
				if (is_string($mime) && preg_match($regexp, $mime, $matches))
				{
					$this->file_type = $matches[1];
					return;
				}
			}
		}

		if (DIRECTORY_SEPARATOR !== '\\')
		{
			$cmd = 'file --brief --mime '.escapeshellarg($file['tmp_name']).' 2>&1';
            @exec($cmd, $mime_out, $return_status);
            if ($return_status === 0 && isset($mime_out[0]) && is_string($mime_out[0]) && preg_match($regexp, $mime_out[0], $matches))
            {
                $this->file_type = $matches[1];
                return;
            }
		}

		if (function_exists('mime_content_type'))
		{
			$this->file_type = @mime_content_type($file['tmp_name']);
			if (strlen((string)$this->file_type) > 0)
			{
				return;
			}
		}
		$this->file_type = $file['type'];
	}
}
