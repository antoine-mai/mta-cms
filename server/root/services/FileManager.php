<?php namespace Root\Services;
/**
 * FileManager Service
 * Handles all file system operations for the root panel.
**/
use \Root\Core\Registry;
/**
 * 
**/
class FileManager
{
    /**
     * @var \Root\Core\Config
     */
    protected $config;

    /**
     * @var string
     */
    protected $projectRoot;

    public function __construct()
    {
        $this->config = Registry::getInstance('Config');
        $this->projectRoot = $this->config->getRootDir();
    }

    /**
     * List files and directories in a given path
     */
    public function browse(string $reqPath)
    {
        if (strpos($reqPath, '..') !== false) {
            throw new \Exception('Invalid path', 400);
        }

        $realPath = realpath($this->projectRoot . '/' . $reqPath);

        if (!$realPath || strpos($realPath, $this->projectRoot) !== 0) {
            $realPath = $this->projectRoot;
            $reqPath = '/';
        }

        if (!is_dir($realPath)) {
            throw new \Exception('Directory not found', 404);
        }

        $items = [];
        $scanned = scandir($realPath);

        foreach ($scanned as $item) {
            if ($item === '.' || $item === '..') continue;
            if ($item === '.git') continue;

            $full = $realPath . '/' . $item;
            $isDir = is_dir($full);
            
            $size = '-';
            if (!$isDir) {
                $bytes = filesize($full);
                $units = ['B', 'KB', 'MB', 'GB'];
                $factor = floor((strlen($bytes) - 1) / 3);
                $size = sprintf("%.2f", $bytes / pow(1024, $factor)) . " " . @$units[$factor];
            }

            $items[] = [
                'name' => $item,
                'path' => rtrim($reqPath, '/') . '/' . $item,
                'type' => $isDir ? 'dir' : 'file',
                'size' => $size,
                'modified' => date('Y-m-d H:i', filemtime($full))
            ];
        }

        usort($items, function($a, $b) {
            if ($a['type'] !== $b['type']) {
                return $a['type'] === 'dir' ? -1 : 1;
            }
            return strcasecmp($a['name'], $b['name']);
        });

        return $items;
    }

    /**
     * Read file content
     */
    public function read(string $reqPath)
    {
        $realPath = $this->validatePath($reqPath);

        if (!file_exists($realPath) || is_dir($realPath)) {
            throw new \Exception('File not found', 404);
        }

        return file_get_contents($realPath);
    }

    /**
     * Save file content
     */
    public function save(string $reqPath, string $content)
    {
        $realPath = $this->validatePath($reqPath);

        if (is_dir($realPath)) {
            throw new \Exception('Cannot save to a directory', 400);
        }

        if (file_exists($realPath) && !is_writable($realPath)) {
            throw new \Exception('File is not writable', 403);
        }

        if (file_put_contents($realPath, $content) === false) {
            throw new \Exception('Failed to save file', 500);
        }

        return true;
    }

    /**
     * Create a new file or directory
     */
    public function create(string $parentPath, string $name, string $type = 'file')
    {
        if (strpos($name, '/') !== false || strpos($name, '..') !== false) {
            throw new \Exception('Invalid name', 400);
        }

        $realParent = $this->validatePath($parentPath);
        if (!is_dir($realParent)) {
            throw new \Exception('Parent is not a directory', 400);
        }

        $targetPath = $realParent . '/' . $name;

        if (file_exists($targetPath)) {
            throw new \Exception('Item already exists', 409);
        }

        if ($type === 'dir') {
            if (!mkdir($targetPath, 0755)) {
                throw new \Exception('Failed to create directory', 500);
            }
        } else {
            if (file_put_contents($targetPath, '') === false) {
                throw new \Exception('Failed to create file', 500);
            }
        }

        return true;
    }

    /**
     * Rename a file or directory
     */
    public function rename(string $path, string $newName)
    {
        if (strpos($newName, '/') !== false || strpos($newName, '..') !== false) {
            throw new \Exception('Invalid name', 400);
        }

        $oldPath = $this->validatePath($path);
        $newPath = dirname($oldPath) . '/' . $newName;

        if (file_exists($newPath)) {
            throw new \Exception('Item already exists', 409);
        }

        if (!rename($oldPath, $newPath)) {
            throw new \Exception('Failed to rename item', 500);
        }

        return true;
    }

    /**
     * Delete a file or directory (recursive)
     */
    public function delete(string $path)
    {
        $realPath = $this->validatePath($path);

        if ($realPath === $this->projectRoot) {
            throw new \Exception('Cannot delete project root', 403);
        }

        if (is_dir($realPath)) {
            if (!$this->deleteRecursive($realPath)) {
                throw new \Exception('Failed to delete directory', 500);
            }
        } else {
            if (!unlink($realPath)) {
                throw new \Exception('Failed to delete file', 500);
            }
        }

        return true;
    }

    /**
     * Copy a file or directory
     */
    public function copy(string $source, string $destinationDir)
    {
        $srcPath = $this->validatePath($source);
        $destDir = $this->validatePath($destinationDir);

        if (!is_dir($destDir)) {
            throw new \Exception('Destination must be a directory', 400);
        }

        $destPath = $destDir . '/' . basename($srcPath);

        if (file_exists($destPath)) {
            throw new \Exception('Item already exists at destination', 409);
        }

        if (is_dir($srcPath)) {
            if (!$this->copyRecursive($srcPath, $destPath)) {
                throw new \Exception('Failed to copy directory', 500);
            }
        } else {
            if (!copy($srcPath, $destPath)) {
                throw new \Exception('Failed to copy file', 500);
            }
        }

        return true;
    }

    /**
     * Move (Cut & Paste) a file or directory
     */
    public function move(string $source, string $destinationDir)
    {
        $srcPath = $this->validatePath($source);
        $destDir = $this->validatePath($destinationDir);

        if (!is_dir($destDir)) {
            throw new \Exception('Destination must be a directory', 400);
        }

        $destPath = $destDir . '/' . basename($srcPath);

        if (file_exists($destPath)) {
            throw new \Exception('Item already exists at destination', 409);
        }

        if (!rename($srcPath, $destPath)) {
            throw new \Exception('Failed to move item', 500);
        }

        return true;
    }

    /**
     * Prepare a file or directory for download by zipping it
     * Returns the path to the temporary zip file
     */
    public function download(string $path)
    {
        $realPath = $this->validatePath($path);

        if (!file_exists($realPath)) {
            throw new \Exception('Item not found', 404);
        }

        $tempZip = sys_get_temp_dir() . '/' . uniqid('download_', true) . '.zip';
        $zip = new \ZipArchive();

        if ($zip->open($tempZip, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
            throw new \Exception('Could not create zip archive', 500);
        }

        if (is_dir($realPath)) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($realPath),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($realPath) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }
        } else {
            $zip->addFile($realPath, basename($realPath));
        }

        $zip->close();

        if (!file_exists($tempZip)) {
            throw new \Exception('Failed to create zip file', 500);
        }

        return $tempZip;
    }

    /**
     * Upload a file to a destination directory
     */
    public function upload(string $path, array $file)
    {
        $targetDir = $this->validatePath($path);

        if (!is_dir($targetDir)) {
            throw new \Exception('Destination must be a directory', 400);
        }

        $fileName = basename($file['name']);
        // Basic filename cleanup
        $fileName = preg_replace('/[^a-zA-Z0-9._-]/', '', $fileName);
        
        $targetFile = $targetDir . '/' . $fileName;

        if (file_exists($targetFile)) {
            throw new \Exception('File already exists', 409);
        }

        if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
            throw new \Exception('Failed to move uploaded file', 500);
        }

        return true;
    }

    /**
     * Helper: Validate and return realpath within project root
     */
    protected function validatePath(string $path): string
    {
        if (strpos($path, '..') !== false) {
            throw new \Exception('Invalid path', 400);
        }

        $fullPath = $this->projectRoot . '/' . ltrim($path, '/');
        // realpath might return false if file doesn't exist, so we only use it if it exists
        if (file_exists($fullPath)) {
            $real = realpath($fullPath);
            if (!$real || strpos($real, $this->projectRoot) !== 0) {
                throw new \Exception('Invalid path access denied', 403);
            }
            return $real;
        }

        // For paths that don't exist yet (like new files/dirs), we check if parent is valid
        $parent = realpath(dirname($fullPath));
        if (!$parent || strpos($parent, $this->projectRoot) !== 0) {
             throw new \Exception('Invalid path access denied', 403);
        }

        return $fullPath;
    }

    /**
     * Recursive delete
     */
    protected function deleteRecursive($dir): bool
    {
        foreach (scandir($dir) as $item) {
            if ($item === '.' || $item === '..') continue;
            $full = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($full)) {
                if (!$this->deleteRecursive($full)) return false;
            } else {
                if (!unlink($full)) return false;
            }
        }
        return rmdir($dir);
    }

    /**
     * Recursive copy
     */
    protected function copyRecursive($src, $dst): bool
    {
        if (!mkdir($dst, 0755, true)) return false;
        foreach (scandir($src) as $item) {
            if ($item === '.' || $item === '..') continue;
            $srcItem = $src . DIRECTORY_SEPARATOR . $item;
            $dstItem = $dst . DIRECTORY_SEPARATOR . $item;
            if (is_dir($srcItem)) {
                if (!$this->copyRecursive($srcItem, $dstItem)) return false;
            } else {
                if (!copy($srcItem, $dstItem)) return false;
            }
        }
        return true;
    }
}
