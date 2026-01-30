<?php
/**
 * Autoload file
 * 
 * Includes the Loader class and starts the autoloader.
 */
require_once __DIR__ . '/core/Loader.php';
require_once __DIR__ . '/core/Helpers.php';

/**
 * Autoloads classes.
**/
\Root\Core\Loader::autoload();

/**
 * Load Startup class
**/
require_once __DIR__ . '/core/Startup.php';