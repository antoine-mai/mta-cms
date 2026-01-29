<?php namespace Admin\Core;
/**
 * Exceptions Class
 *
 * Handles display and logging of errors and exceptions.
**/
class Exceptions
{
    /**
     * Nesting level of output buffering
     *
     * @var int
     */
    public $obLevel;

    /**
     * List of PHP error levels
     *
     * @var array
     */
    public $levels = [
        E_ERROR             => 'Error',
        E_WARNING           => 'Warning',
        E_PARSE             => 'Parsing Error',
        E_NOTICE            => 'Notice',
        E_CORE_ERROR        => 'Core Error',
        E_CORE_WARNING      => 'Core Warning',
        E_COMPILE_ERROR     => 'Compile Error',
        E_COMPILE_WARNING   => 'Compile Warning',
        E_USER_ERROR        => 'User Error',
        E_USER_WARNING      => 'User Warning',
        E_USER_NOTICE       => 'User Notice'
    ];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->obLevel = ob_get_level();
    }

    /**
     * Log Exception
     */
    public function logException($severity, $message, $filepath, $line)
    {
        $severityName = isset($this->levels[$severity]) ? $this->levels[$severity] : $severity;
        Error::logMessage('error', 'Severity: ' . $severityName . ' --> ' . $message . ' ' . $filepath . ' ' . $line);
    }

    /**
     * Show 404 Page
     */
    public function show404($page = '', $logError = true)
    {
        if (Console::isCli()) {
            $heading = 'Not Found';
            $message = 'The controller/method pair you requested was not found.';
        } else {
            $heading = '404 Page Not Found';
            $message = 'The page you requested was not found.';
        }

        if ($logError) {
            Error::logMessage('error', $heading . ': ' . $page);
        }

        echo $this->showError($heading, $message, 'error_404', 404);
        exit(4); // EXIT_UNKNOWN_FILE
    }

    /**
     * Show Error Page
     */
    public function showError($heading, $message, $template = 'error_general', $statusCode = 500)
    {
        $templatesPath = Common::configItem('error_views_path');
        if (empty($templatesPath)) {
            $templatesPath = ADMIN_ROOT . 'template/errors' . DIRECTORY_SEPARATOR;
        }

        if (Console::isCli()) {
            $message = "\t" . (is_array($message) ? implode("\n\t", $message) : $message);
            $template = 'cli' . DIRECTORY_SEPARATOR . $template;
        } else {
            Error::setStatusHeader($statusCode);
            $message = '<p>' . (is_array($message) ? implode('</p><p>', $message) : $message) . '</p>';
            $template = 'html' . DIRECTORY_SEPARATOR . $template;
        }

        if (ob_get_level() > $this->obLevel + 1) {
            ob_end_flush();
        }
        ob_start();
        include($templatesPath . $template . '.php');
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;
    }

    /**
     * Show Exception Page
     */
    public function showException($exception)
    {
        $templatesPath = Common::configItem('error_views_path');
        if (empty($templatesPath)) {
            $templatesPath = ADMIN_ROOT . 'template/errors' . DIRECTORY_SEPARATOR;
        }

        $message = $exception->getMessage();
        if (empty($message)) {
            $message = '(null)';
        }

        if (Console::isCli()) {
            $templatesPath .= 'cli' . DIRECTORY_SEPARATOR;
        } else {
            $templatesPath .= 'html' . DIRECTORY_SEPARATOR;
        }

        if (ob_get_level() > $this->obLevel + 1) {
            ob_end_flush();
        }
        ob_start();
        include($templatesPath . 'error_exception.php');
        $buffer = ob_get_contents();
        ob_end_clean();
        echo $buffer;
    }

    /**
     * Show PHP Error Page
     */
    public function showPhpError($severity, $message, $filepath, $line)
    {
        $templatesPath = Common::configItem('error_views_path');
        if (empty($templatesPath)) {
            $templatesPath = ADMIN_ROOT . 'template/errors' . DIRECTORY_SEPARATOR;
        }

        $severityName = isset($this->levels[$severity]) ? $this->levels[$severity] : $severity;

        if (!Console::isCli()) {
            $filepath = str_replace('\\', '/', $filepath);
            if (false !== strpos($filepath, '/')) {
                $x = explode('/', $filepath);
                $filepath = $x[count($x) - 2] . '/' . end($x);
            }
            $template = 'html' . DIRECTORY_SEPARATOR . 'error_php';
        } else {
            $template = 'cli' . DIRECTORY_SEPARATOR . 'error_php';
        }

        if (ob_get_level() > $this->obLevel + 1) {
            ob_end_flush();
        }
        ob_start();
        include($templatesPath . $template . '.php');
        $buffer = ob_get_contents();
        ob_end_clean();
        echo $buffer;
    }
}
