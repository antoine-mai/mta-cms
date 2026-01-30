<?php namespace Root\Pages\Post\System;

use Root\Core\Controller;
use Root\Core\Request\Request;
use Root\Core\Response\Response;

class Composer extends Controller
{
    /**
     * POST /root/post/system/composer
     * Install composer
     */
    public function index(Request $request): Response
    {
        @set_time_limit(300);
        $result = $this->installComposer();
        
        if ($result === true) {
            return $this->json(['success' => true]);
        } else {
            return $this->json([ 'success' => false, 'message' => $result], 200);
        }
    }

    /**
     * POST /root/post/system/composer/fix
     * Run composer update to fix version mismatches
     */
    public function fix(Request $request): Response
    {
        @set_time_limit(600); // 10 minutes for update
        $projectRoot = $this->config->getRootDir();
        $composerPath = $projectRoot . '/bin/composer';

        // 1. Ensure composer is installed
        if (!file_exists($composerPath)) {
            $installResult = $this->installComposer();
            if ($installResult !== true) {
                return $this->json([ 'success' => false, 'message' => 'Composer missing and auto-install failed: ' . $installResult], 200);
            }
        }

        // 2. Run composer update in server directory
        $serverDir = $projectRoot . '/server';
        if (!is_dir($serverDir)) {
            return $this->json([ 'success' => false, 'message' => 'Server directory not found'], 200);
        }

        chdir($serverDir);
        
        $output = [];
        $returnCode = 0;
        // Run composer update with --no-interaction and --optimize-autoloader
        exec("php $composerPath update --no-interaction --optimize-autoloader 2>&1", $output, $returnCode);

        if ($returnCode === 0) {
            return $this->json(['success' => true]);
        } else {
            return $this->json([ 'success' => false, 'message' => 'Composer update failed: ' . implode("\n", $output)], 200);
        }
    }

    private function installComposer()
    {
        $projectRoot = $this->config->getRootDir();
        $binDir = $projectRoot . '/bin';
        
        if (!is_dir($binDir)) {
            if (!mkdir($binDir, 0755, true)) {
                return 'Failed to create bin directory: ' . $binDir;
            }
        }

        chdir($binDir);

        $installerHash = 'c8b085408188070d5f52bcfe4ecfbee5f727afa458b2573b8eaaf77b3419b0bf2768dc67c86944da1544f06fa544fd47';
        $setupFile = 'composer-setup.php';

        if (!@copy('https://getcomposer.org/installer', $setupFile)) {
            return 'Failed to download composer-setup.php. Check your internet connection or allow_url_fopen.';
        }

        if (hash_file('sha384', $setupFile) !== $installerHash) {
            unlink($setupFile);
            return 'Installer corrupt - Hash mismatch.';
        }

        $output = [];
        $returnCode = 0;
        exec("php $setupFile --filename=composer", $output, $returnCode);

        if (file_exists($setupFile)) {
            unlink($setupFile);
        }

        if ($returnCode === 0 && file_exists($binDir . '/composer')) {
            chmod($binDir . '/composer', 0755);
            return true;
        } else {
            return 'Installer failed: ' . implode("\n", $output);
        }
    }
}
