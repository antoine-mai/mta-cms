<?php declare(strict_types=1);
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
/**
 * Check if the application is installed
 * @return bool
 */
/**
 * Check if the application is installed
 * @return bool
 */
function isInstalled(): bool
{
    $envPath = ROOT_DIR . '/.env';
    $defaultUser = 'admin';
    // Legacy insecure default to check against
    $insecurePass = 'admin@123';

    if (!file_exists($envPath)) {
        // Generate random strong password
        try {
            $randomPass = bin2hex(random_bytes(12));
        } catch (\Exception $e) {
            $randomPass = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 24);
        }

        $content = 'DATABASE_URL="sqlite:///%kernel.project_dir%/storage/data/app.db"' . "\n";
        $content .= 'ADMIN_USER=' . $defaultUser . "\n";
        $content .= 'ADMIN_PASS=' . $randomPass . "\n";
        file_put_contents($envPath, $content);
        
        // Return false to trigger redirect on first run so user goes to admin
        return false;
    }

    $envContent = file_get_contents($envPath);
    
    // Check if legacy insecure credentials are still in use
    if (preg_match('/ADMIN_USER\s*=\s*' . preg_quote($defaultUser, '/') . '\s*(\n|$)/', $envContent) && 
        preg_match('/ADMIN_PASS\s*=\s*' . preg_quote($insecurePass, '/') . '\s*(\n|$)/', $envContent)) {
        return false;
    }

    return true;
}

if (!isInstalled()) {
    header('Location: /admin');
    exit;
}

\App\Startup::run();