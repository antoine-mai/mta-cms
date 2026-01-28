<?php namespace Admin\Core;
/**
 * 
**/
#[\AllowDynamicProperties]
class Template
{
    protected $view_paths = [ADMIN_ROOT . 'template/' => true];
    protected $cached_vars = [];
    protected $ob_level;

    public function __construct()
    {
        $this->ob_level = ob_get_level();
        \Admin\Core\Error::logMessage('info', 'Template Class Initialized');
    }

    public function load($view, $vars = [], $return = false)
    {
        return $this->_load(['view' => $view, 'vars' => $this->prepare_vars($vars), 'return' => $return]);
    }

    protected function _load($data)
    {
        foreach (['view', 'vars', 'path', 'return'] as $val)
        {
            $$val = isset($data[$val]) ? $data[$val] : false;
        }

        $file_exists = false;
        if (is_string($path) && $path !== '')
        {
            $x = explode('/', $path);
            $file = end($x);
        }
        else
        {
            $ext = pathinfo($view, PATHINFO_EXTENSION);
            $file = ($ext === '') ? $view.'.php' : $view;
            foreach ($this->view_paths as $view_file => $cascade)
            {
                if (file_exists($view_file.$file))
                {
                    $path = $view_file.$file;
                    $file_exists = true;
                    break;
                }
                if ( ! $cascade)
                {
                    break;
                }
            }
        }

        if ( ! $file_exists && ! file_exists($path))
        {
            \Admin\Core\Error::showError('Unable to load the requested file: '.$file);
        }

        // Make the CI request object available to views
        $CI =& getInstance();
        foreach (get_object_vars($CI) as $key => $var)
        {
            if ( ! isset($this->$key))
            {
                $this->$key =& $CI->$key;
            }
        }

        empty($vars) OR $this->cached_vars = array_merge($this->cached_vars, $vars);
        extract($this->cached_vars);

        ob_start();

        if ( ! \Admin\Core\Common::isPhp('5.4') && ! ini_get('short_open_tag') && \Admin\Core\Common::configItem('rewrite_short_tags') === true)
        {
            echo eval('?>'.preg_replace('/;*\s*\?>/', '; ?>', str_replace('<?=', '<?php echo ', file_get_contents($path))));
        }
        else
        {
            include($path); 
        }

        \Admin\Core\Error::logMessage('info', 'File loaded: '.$path);

        if ($return === true)
        {
            $buffer = ob_get_contents();
            @ob_end_clean();
            return $buffer;
        }

        if (ob_get_level() > $this->ob_level + 1)
        {
            ob_end_flush();
        }
        else
        {
            // For now, adhere to old Output buffering if it exists, or just flush
            // If we are strictly using Response objects now, this might change, 
            // but for backward compat, we might still want to capture it.
            // However, the user is moving to Response, so $return=true is the main use case.
            // But if user does NOT use return=true, we should probably output.
            
            if (isset($CI->output)) {
                $CI->output->appendOutput(ob_get_contents());
            } else {
                 echo ob_get_contents();
            }
           
            @ob_end_clean();
        }

        return $this;
    }

	protected function prepare_vars($vars)
	{
		if ( ! is_array($vars))
		{
			$vars = is_object($vars)
				? get_object_vars($vars)
				: [];
		}
		foreach (array_keys($vars) as $key)
		{
			if (strncmp($key, 'ci_', 3) === 0)
			{
				unset($vars[$key]);
			}
		}
		return $vars;
	}
}
