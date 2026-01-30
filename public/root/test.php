<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "PHP is working\n";
echo "ROOT_DIR: " . (defined('ROOT_DIR') ? ROOT_DIR : 'NOT DEFINED') . "\n";

define('ROOT_DIR', dirname(__DIR__, 2));
echo "ROOT_DIR after define: " . ROOT_DIR . "\n";

$autoloadPath = ROOT_DIR . '/server/root/autoload.php';
echo "Autoload path: $autoloadPath\n";
echo "File exists: " . (file_exists($autoloadPath) ? 'YES' : 'NO') . "\n";

try {
    require_once $autoloadPath;
    echo "Autoload loaded successfully!\n";
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
