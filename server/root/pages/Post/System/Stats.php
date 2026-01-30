<?php namespace Root\Pages\Post\System;

use Root\Core\Controller;
use Root\Core\Request\Request;
use Root\Core\Response\Response;

class Stats extends Controller
{
    public function index(Request $request): Response
    {
        // 1. Snapshot 1
        $cpu1 = $this->getServerLoad();
        $net1 = $this->getNetworkStats();

        // 2. Wait for delta
        usleep(100000); // 100ms

        // 3. Snapshot 2
        $cpu2 = $this->getServerLoad();
        $net2 = $this->getNetworkStats();

        // 4. Calculate Difference
        $cpuDiff = [
            'total' => $cpu2['total'] - $cpu1['total'],
            'idle' => $cpu2['idle'] - $cpu1['idle']
        ];
        $cpuPercent = ($cpuDiff['total'] > 0) ? round((1 - ($cpuDiff['idle'] / $cpuDiff['total'])) * 100, 1) : 0;

        // Network Rate (Bytes per second) = (Diff / 0.1s) = Diff * 10
        $rxRate = ($net2['rx'] - $net1['rx']) * 10;
        $txRate = ($net2['tx'] - $net1['tx']) * 10;

        // 5. Memory & Disk Logic (Instant)
        $memTotal = 0;
        $memFree = 0;
        $swapTotal = 0;
        $swapFree = 0;

        if (is_readable('/proc/meminfo')) {
            $meminfo = file_get_contents('/proc/meminfo');
            if (preg_match('/MemTotal:\s+(\d+)\s+kB/', $meminfo, $matches)) $memTotal = $matches[1] / 1024; // MB
            if (preg_match('/MemAvailable:\s+(\d+)\s+kB/', $meminfo, $matches)) $memFree = $matches[1] / 1024; // MB
            if (preg_match('/SwapTotal:\s+(\d+)\s+kB/', $meminfo, $matches)) $swapTotal = $matches[1] / 1024; // MB
            if (preg_match('/SwapFree:\s+(\d+)\s+kB/', $meminfo, $matches)) $swapFree = $matches[1] / 1024; // MB
        }
        
        $memUsed = $memTotal - $memFree;
        $swapUsed = $swapTotal - $swapFree;

        $diskTotal = disk_total_space("/");
        $diskFree = disk_free_space("/");
        $diskUsed = $diskTotal - $diskFree;

        // Read required php version from home/composer.json
        // Read required php version from server/composer.json
        $requiredPhp = 'Unknown';
        
        $projectRoot = $this->config->getRootDir();
        $projectRoot = rtrim($projectRoot, '/');
        
        $composerBinPath = $projectRoot . '/bin/composer';
        $composerPharPath = $projectRoot . '/bin/composer.phar';
        
        clearstatcache(true, $composerBinPath);
        clearstatcache(true, $composerPharPath);
        
        $composerInstalled = is_file($composerBinPath) || is_file($composerPharPath);
        
        // Check for required PHP version in server/composer.json (fallback to server/home/composer.json)
        $composerJsonPaths = [
            $projectRoot . '/server/composer.json',
            $projectRoot . '/server/home/composer.json'
        ];
        
        foreach ($composerJsonPaths as $path) {
            if (file_exists($path)) {
                $composer = json_decode(file_get_contents($path), true);
                if (isset($composer['require']['php'])) {
                    $requiredPhp = $composer['require']['php'];
                    break;
                }
            }
        }
        
        return $this->json([
            'os' => php_uname('s') . ' ' . php_uname('r'),
            'php_version' => PHP_VERSION,
            'required_php_version' => $requiredPhp,
            'composer_installed' => $composerInstalled,
            'php_modules' => get_loaded_extensions(),
            'cpu' => [
                'percent' => $cpuPercent,
                'load' => sys_getloadavg()
            ],
            'network' => [
                'rx_kbs' => round($rxRate / 1024, 1),
                'tx_kbs' => round($txRate / 1024, 1)
            ],
            'memory' => [
                'total' => round($memTotal / 1024, 2) . ' GB',
                'used' => round($memUsed / 1024, 2) . ' GB',
                'percent' => $memTotal > 0 ? round(($memUsed / $memTotal) * 100, 1) : 0
            ],
            'swap' => [
                'total' => round($swapTotal / 1024, 2) . ' GB',
                'used' => round($swapUsed / 1024, 2) . ' GB',
                'percent' => $swapTotal > 0 ? round(($swapUsed / $swapTotal) * 100, 1) : 0
            ],
            'storage' => [
                'total' => round($diskTotal / 1024 / 1024 / 1024, 2) . ' GB',
                'used' => round($diskUsed / 1024 / 1024 / 1024, 2) . ' GB',
                'percent' => $diskTotal > 0 ? round(($diskUsed / $diskTotal) * 100, 1) : 0
            ],
            'php_ini' => [
                'memory_limit' => ini_get('memory_limit'),
                'post_max_size' => ini_get('post_max_size'),
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'max_execution_time' => ini_get('max_execution_time'),
                'max_input_vars' => ini_get('max_input_vars'),
                'display_errors' => ini_get('display_errors'),
                'error_reporting' => ini_get('error_reporting'),
                'date_timezone' => ini_get('date.timezone')
            ]
        ]);
    }

    private function getServerLoad()
    {
        $total = 0;
        $idle = 0;
        if (is_readable('/proc/stat')) {
            $stat = file_get_contents('/proc/stat');
            $lines = explode("\n", $stat);
            foreach ($lines as $line) {
                if (preg_match('/^cpu /', $line)) {
                    $parts = preg_split('/\s+/', trim($line));
                    // parts[0] is 'cpu'
                    // user, nice, system, idle, iowait, irq, softirq, steal
                    $total = $parts[1] + $parts[2] + $parts[3] + $parts[4] + $parts[5] + $parts[6] + $parts[7] + $parts[8];
                    $idle = $parts[4] + $parts[5]; // idle + iowait
                    break;
                }
            }
        }
        return ['total' => $total, 'idle' => $idle];
    }

    private function getNetworkStats()
    {
        $rx = 0;
        $tx = 0;
        if (is_readable('/proc/net/dev')) {
            $lines = file('/proc/net/dev');
            foreach ($lines as $line) {
                if (strpos($line, ':') !== false) {
                    $parts = preg_split('/\s+/', trim($line));
                    // Format: interface: rx_bytes ... tx_bytes ...
                    // parts logic is tricky due to spaces. simple regex is better.
                    if (preg_match('/^([^:]+):\s*(\d+)\s+\d+\s+\d+\s+\d+\s+\d+\s+\d+\s+\d+\s+\d+\s+(\d+)/', trim($line), $matches)) {
                        // Skip loopback
                        if ($matches[1] !== 'lo') {
                            $rx += $matches[2];
                            $tx += $matches[3];
                        }
                    }
                }
            }
        }
        return ['rx' => $rx, 'tx' => $tx];
    }
}
