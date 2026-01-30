<?php namespace Root\Core;

/**
 * Error Class
 * 
 * Provides static methods for error handling and logging.
 */
class Error
{
    /**
     * Log an error message
     * 
     * @param string $message
     * @return void
     */
    public static function error_log($message)
    {
        // Native PHP error log
        @error_log($message);

        // If Logging is initialized, write to application logs
        try {
            if (class_exists('\\Root\\Core\\Registry', false)) {
                $logging = &Registry::getInstance('Logging');
                if ($logging) {
                    $logging->writeLog('error', $message);
                }
            }
        } catch (\Exception $e) {
            // Silently fail if logging is not ready
        }
    }
}
