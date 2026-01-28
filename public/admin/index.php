<?php
/**
 * CodeIgniter 3 Entry Point - Simplified
 */

error_reporting(-1);
ini_set('display_errors', 1);
define ('ROOT_DIR   ', __DIR__ . '/../../');
define ('ADMIN_ROOT', __DIR__ . '/../../admin/');

$admin_path = realpath(__DIR__ . '/../../admin/') . DIRECTORY_SEPARATOR;

define('APPPATH', $admin_path);
define('VIEWPATH', $admin_path . 'views' . DIRECTORY_SEPARATOR);
define('CONFPATH', realpath(__DIR__ . '/../../config/admin') . DIRECTORY_SEPARATOR);

require_once ADMIN_ROOT . 'core/Admin.php';
