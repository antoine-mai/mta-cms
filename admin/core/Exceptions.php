<?php namespace Admin\Core;
/**
 * 
 */
#[\AllowDynamicProperties]
class Exceptions {
	public $ob_level;
	public $levels = [
		E_ERROR			=>	'Error',
		E_WARNING		=>	'Warning',
		E_PARSE			=>	'Parsing Error',
		E_NOTICE		=>	'Notice',
		E_CORE_ERROR		=>	'Core Error',
		E_CORE_WARNING		=>	'Core Warning',
		E_COMPILE_ERROR		=>	'Compile Error',
		E_COMPILE_WARNING	=>	'Compile Warning',
		E_USER_ERROR		=>	'User Error',
		E_USER_WARNING		=>	'User Warning',
		E_USER_NOTICE		=>	'User Notice'
	];
	public function __construct()
	{
		$this->ob_level = ob_get_level();
	}
	public function logException($severity, $message, $filepath, $line)
	{
		$severity = isset($this->levels[$severity]) ? $this->levels[$severity] : $severity;
		\Admin\Core\Error::logMessage('error', 'Severity: '.$severity.' --> '.$message.' '.$filepath.' '.$line);
	}
	public function show404($page = '', $log_error = true)
	{
        if (Console::isCli())
		{
			$heading = 'Not Found';
			$message = 'The controller/method pair you requested was not found.';
		}
		else
		{
			$heading = '404 Page Not Found';
			$message = 'The page you requested was not found.';
		}
		if ($log_error)
		{
			\Admin\Core\Error::logMessage('error', $heading.': '.$page);
		}
		echo $this->showError($heading, $message, 'error_404', 404);
		exit(4); // EXIT_UNKNOWN_FILE
	}
	public function showError($heading, $message, $template = 'error_general', $status_code = 500)
	{
		$templates_path = \Admin\Core\Common::configItem('error_views_path');
		if (empty($templates_path))
		{
			$templates_path = ADMIN_ROOT.'template/errors'.DIRECTORY_SEPARATOR;
		}
        if (Console::isCli())
		{
			$message = "\t".(is_array($message) ? implode("\n\t", $message) : $message);
			$template = 'cli'.DIRECTORY_SEPARATOR.$template;
		}
		else
		{
			\Admin\Core\Error::setStatusHeader($status_code);
			$message = '<p>'.(is_array($message) ? implode('</p><p>', $message) : $message).'</p>';
			$template = 'html'.DIRECTORY_SEPARATOR.$template;
		}
		if (ob_get_level() > $this->ob_level + 1)
		{
			ob_end_flush();
		}
		ob_start();
		include($templates_path.$template.'.php');
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	}
	public function showException($exception)
	{
		$templates_path = \Admin\Core\Common::configItem('error_views_path');
		if (empty($templates_path))
		{
			$templates_path = ADMIN_ROOT.'template/errors'.DIRECTORY_SEPARATOR;
		}
		$message = $exception->getMessage();
		if (empty($message))
		{
			$message = '(null)';
		}
        if (Console::isCli())
		{
			$templates_path .= 'cli'.DIRECTORY_SEPARATOR;
		}
		else
		{
			$templates_path .= 'html'.DIRECTORY_SEPARATOR;
		}
		if (ob_get_level() > $this->ob_level + 1)
		{
			ob_end_flush();
		}
		ob_start();
		include($templates_path.'error_exception.php');
		$buffer = ob_get_contents();
		ob_end_clean();
		echo $buffer;
	}
	public function showPhpError($severity, $message, $filepath, $line)
	{
		$templates_path = \Admin\Core\Common::configItem('error_views_path');
		if (empty($templates_path))
		{
			$templates_path = ADMIN_ROOT.'template/errors'.DIRECTORY_SEPARATOR;
		}
		$severity = isset($this->levels[$severity]) ? $this->levels[$severity] : $severity;
		if ( ! Console::isCli())
		{
			$filepath = str_replace('\\', '/', $filepath);
			if (false !== strpos($filepath, '/'))
			{
				$x = explode('/', $filepath);
				$filepath = $x[count($x)-2].'/'.end($x);
			}
			$template = 'html'.DIRECTORY_SEPARATOR.'error_php';
		}
		else
		{
			$template = 'cli'.DIRECTORY_SEPARATOR.'error_php';
		}
		if (ob_get_level() > $this->ob_level + 1)
		{
			ob_end_flush();
		}
		ob_start();
		include($templates_path.$template.'.php');
		$buffer = ob_get_contents();
		ob_end_clean();
		echo $buffer;
	}
}
