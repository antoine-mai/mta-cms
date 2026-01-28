<?php declare(strict_types=1);
/**
 * 
**/
ini_set('display_errors', '1');
error_reporting(E_ALL);
/**
 * 
**/
define ('ROOT_DIR', dirname(__DIR__));
/**
 * 
**/
require ROOT_DIR . '/server/startup.php';
/**
 *
**/
\App\Startup::run();