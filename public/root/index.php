<?php
/**
 * 
**/
define ('ROOT_DIR', dirname(__DIR__, 2));
/**
 * 
**/
require_once ROOT_DIR . '/server/root/autoload.php';
/**
 * 
**/
\Root\Core\Startup::run();