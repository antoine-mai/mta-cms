<?php namespace Root\Core;
/**
 * Logging Class
**/
class Logging
{
    protected $_log_path;
    protected $_file_permissions = 0644;
    protected $_threshold = 1;
    protected $_threshold_array = [];
    protected $_date_fmt = 'Y-m-d H:i:s';
    protected $_file_ext;
    protected $_enabled = true;
    protected $_levels = ['ERROR' => 1, 'DEBUG' => 2, 'INFO' => 3, 'ALL' => 4];

    public function __construct()
    {
        $config = &Registry::getInstance('Config');

        $this->_log_path = ($config->item('path', 'log') !== '') ? $config->item('path', 'log') : $config->getRootPath() . 'logs/';
        $this->_file_ext = ($config->item('fileExtension', 'log') !== '')
            ? ltrim((string)$config->item('fileExtension', 'log'), '.') : 'php';

        if (!file_exists($this->_log_path)) {
            mkdir($this->_log_path, 0755, true);
        }

        if (!is_dir($this->_log_path) || !Common::isReallyWritable($this->_log_path)) {
            $this->_enabled = false;
        }

        if (is_numeric($config->item('threshold', 'log'))) {
            $this->_threshold = (int)$config->item('threshold', 'log');
        } elseif (is_array($config->item('threshold', 'log'))) {
            $this->_threshold = 0;
            $this->_threshold_array = array_flip($config->item('threshold', 'log'));
        }

        if ($config->item('dateFormat', 'log') !== '') {
            $this->_date_fmt = $config->item('dateFormat', 'log');
        }

        if ($config->item('filePermissions', 'log') && is_int($config->item('filePermissions', 'log'))) {
            $this->_file_permissions = $config->item('filePermissions', 'log');
        }
    }

    public function writeLog($level, $msg)
    {
        if ($this->_enabled === false) {
            return false;
        }

        $level = strtoupper((string)$level);
        if ((!isset($this->_levels[$level]) || ($this->_levels[$level] > $this->_threshold))
            && !isset($this->_threshold_array[$this->_levels[$level]])
        ) {
            return false;
        }

        $filepath = $this->_log_path . 'log-' . date('Y-m-d') . '.' . $this->_file_ext;
        $message = '';

        if (!file_exists($filepath)) {
            $newfile = true;
            if ($this->_file_ext === 'php') {
                $message .= "<?php defined('ROOT_PATH') OR exit('No direct script access allowed'); ?>\n\n";
            }
        }

        if (!$fp = @fopen($filepath, 'ab')) {
            return false;
        }

        flock($fp, LOCK_EX);

        if (strpos($this->_date_fmt, 'u') !== false) {
            $microtime_full = microtime(true);
            $microtime_short = sprintf("%06d", ($microtime_full - floor($microtime_full)) * 1000000);
            $date = new \DateTime(date('Y-m-d H:i:s.' . $microtime_short, $microtime_full));
            $date = $date->format($this->_date_fmt);
        } else {
            $date = date($this->_date_fmt);
        }

        $message .= $this->_format_line($level, $date, $msg);

        fwrite($fp, $message);
        
        flock($fp, LOCK_UN);
        fclose($fp);

        if (isset($newfile) && $newfile === true) {
            chmod($filepath, $this->_file_permissions);
        }

        return true;
    }

    protected function _format_line($level, $date, $message)
    {
        return $level . ' - ' . $date . ' --> ' . $message . PHP_EOL;
    }
}
