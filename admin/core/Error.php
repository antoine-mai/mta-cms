<?php namespace Admin\Core;

class Error {

    public static function show_error($message, $status_code = 500, $heading = 'An Error Was Encountered')
    {
        $status_code = abs($status_code);
        if ($status_code < 100)
        {
            $exit_status = $status_code + 9; // 9 is EXIT__AUTO_MIN
            $status_code = 500;
        }
        else
        {
            $exit_status = 1; // EXIT_ERROR
        }
        $_error =& Registry::getInstance('Exceptions', 'core');
        echo $_error->show_error($heading, $message, 'error_general', $status_code);
        exit($exit_status);
    }

    public static function show_404($page = '', $log_error = TRUE)
    {
        $_error =& Registry::getInstance('Exceptions', 'core');
        $_error->show_404($page, $log_error);
        exit(4); // EXIT_UNKNOWN_FILE
    }

    public static function log_message($level, $message)
    {
        $log =& Registry::getInstance('Log', 'core');
        $log->write_log($level, $message);
    }

    public static function set_status_header($code = 200, $text = '')
    {
        if (Common::is_cli())
        {
            return;
        }
        if (empty($code) OR ! is_numeric($code))
        {
            self::show_error('Status codes must be numeric', 500);
        }
        if (empty($text))
        {
            is_int($code) OR $code = (int) $code;
            $stati = [
                100	=> 'Continue',
                101	=> 'Switching Protocols',
                200	=> 'OK',
                201	=> 'Created',
                202	=> 'Accepted',
                203	=> 'Non-Authoritative Information',
                204	=> 'No Content',
                205	=> 'Reset Content',
                206	=> 'Partial Content',
                300	=> 'Multiple Choices',
                301	=> 'Moved Permanently',
                302	=> 'Found',
                303	=> 'See Other',
                304	=> 'Not Modified',
                305	=> 'Use Proxy',
                307	=> 'Temporary Redirect',
                400	=> 'Bad Request',
                401	=> 'Unauthorized',
                402	=> 'Payment Required',
                403	=> 'Forbidden',
                404	=> 'Not Found',
                405	=> 'Method Not Allowed',
                406	=> 'Not Acceptable',
                407	=> 'Proxy Authentication Required',
                408	=> 'Request Timeout',
                409	=> 'Conflict',
                410	=> 'Gone',
                411	=> 'Length Required',
                412	=> 'Precondition Failed',
                413	=> 'Request Entity Too Large',
                414	=> 'Request-URI Too Long',
                415	=> 'Unsupported Media Type',
                416	=> 'Requested Range Not Satisfiable',
                417	=> 'Expectation Failed',
                422	=> 'Unprocessable Entity',
                426	=> 'Upgrade Required',
                428	=> 'Precondition Required',
                429	=> 'Too Many Requests',
                431	=> 'Request Header Fields Too Large',
                500	=> 'Internal Server Error',
                501	=> 'Not Implemented',
                502	=> 'Bad Gateway',
                503	=> 'Service Unavailable',
                504	=> 'Gateway Timeout',
                505	=> 'HTTP Version Not Supported',
                511	=> 'Network Authentication Required',
            ];
            if (isset($stati[$code]))
            {
                $text = $stati[$code];
            }
            else
            {
                self::show_error('No status text available. Please check your status code number or supply your own message text.', 500);
            }
        }
        if (strpos(PHP_SAPI, 'cgi') === 0)
        {
            header('Status: '.$code.' '.$text, TRUE);
            return;
        }
        $server_protocol = (isset($_SERVER['SERVER_PROTOCOL']) && in_array($_SERVER['SERVER_PROTOCOL'], ['HTTP/1.0', 'HTTP/1.1', 'HTTP/2', 'HTTP/2.0'], TRUE))
            ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
        header($server_protocol.' '.$code.' '.$text, TRUE, $code);
    }

    public static function _error_handler($severity, $message, $filepath, $line)
    {
        $is_error = (((E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR | E_USER_ERROR) & $severity) === $severity);
        if ($is_error)
        {
            self::set_status_header(500);
        }
        if (($severity & error_reporting()) !== $severity)
        {
            return;
        }
        $_error =& Registry::getInstance('Exceptions', 'core');
        $_error->log_exception($severity, $message, $filepath, $line);
        if (str_ireplace(['off', 'none', 'no', 'false', 'null'], '', ini_get('display_errors')))
        {
            $_error->show_php_error($severity, $message, $filepath, $line);
        }
        if ($is_error)
        {
            exit(1); // EXIT_ERROR
        }
    }

    public static function _exception_handler($exception)
    {
        $_error =& Registry::getInstance('Exceptions', 'core');
        $_error->log_exception('error', 'Exception: '.$exception->getMessage(), $exception->getFile(), $exception->getLine());
        self::set_status_header(500);
        if (str_ireplace(['off', 'none', 'no', 'false', 'null'], '', ini_get('display_errors')))
        {
            $_error->show_exception($exception);
        }
        exit(1); // EXIT_ERROR
    }

    public static function _shutdown_handler()
    {
        $last_error = error_get_last();
        if (isset($last_error) &&
            ($last_error['type'] & (E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING)))
        {
            self::_error_handler($last_error['type'], $last_error['message'], $last_error['file'], $last_error['line']);
        }
    }
}
