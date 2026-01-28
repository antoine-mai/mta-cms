<?php
error_reporting(-1);
ini_set('display_errors', 1);
define ('ROOT_DIR', dirname(__DIR__, 2));
define ('ADMIN_ROOT', ROOT_DIR . '/admin/');
define ('CONFPATH', ROOT_DIR . '/config/admin/');
require_once ADMIN_ROOT . 'autoload.php';
require_once ADMIN_ROOT . 'core/Admin.php';

\Admin\Core\Admin::run();