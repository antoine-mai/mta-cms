<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('ADMIN_ROOT', __DIR__ . '/admin/');
require_once __DIR__ . '/admin/services/Yaml.php';

use Admin\Services\Yaml;

echo "Starting conversion...\n";

if (!class_exists('Admin\Services\Yaml')) {
    die("Class Yaml not found\n");
}

$yaml = new Yaml();

$mimesPath = __DIR__ . '/config/admin/mimes.php';
if (!file_exists($mimesPath)) {
    die("mimes.php not found at $mimesPath\n");
}

$mimes = include $mimesPath;

if (!is_array($mimes)) {
    die("mimes.php did not return an array\n");
}

$yaml->dump(__DIR__ . '/config/admin/mimes.yaml', $mimes);

echo "mimes.yaml created.\n";
