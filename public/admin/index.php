<?php
/**
 * CodeIgniter 3 Entry Point - Simplified
 */

error_reporting(-1);
ini_set('display_errors', 1);
define ('ADMIN_ROOT', __DIR__ . '/../../admin');
$admin_path = realpath(__DIR__ . '/../../admin') . DIRECTORY_SEPARATOR;

define('BASEPATH', $admin_path);
define('APPPATH', $admin_path);
define('VIEWPATH', $admin_path . 'views' . DIRECTORY_SEPARATOR);
define('CONFPATH', realpath(__DIR__ . '/../../config/admin') . DIRECTORY_SEPARATOR);
define('SYSDIR', basename(ADMIN_ROOT));

require_once ADMIN_ROOT . 'core/CodeIgniter.php';
