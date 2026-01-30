<?php namespace Root\Core;
/**
 * Config Class
 *
 * This class contains functions that enable config files to be managed.
**/
use \Root\Services\Yaml;
/**
 * 
**/
class Config
{
    /**
     * List of all loaded config values
     *
     * @var array
     */
    public $config = [];

    /**
     * List of all loaded config files
     *
     * @var array
     */
    public $isLoaded = [];

    /**
     * Root directory path
     *
     * @var string
     */
    public $rootDir;

    /**
     * Root directory path
     *
     * @var string
     */
    public $rootPath;

    /**
     * Constructor
     *
     * Sets the $config data from the primary config.yaml file
     */
    public function __construct()
    {
        // Initialize path properties first so load() can use them if needed
        $this->rootDir = defined('ROOT_DIR') ? ROOT_DIR : realpath(dirname(__DIR__, 3));
        $this->rootPath = defined('ROOT_PATH') ? ROOT_PATH : dirname(__DIR__) . '/';

        // Hardcoded Default Configuration (previously in root.yaml)
        $this->config = [
            'language' => 'english',
            'charset'  => 'UTF-8',
            'log' => [
                'path'           => "../../storage/log/root/",
                'dateFormat'     => "Y-m-d H:i:s",
                'filePermissions' => 0644,
                'fileExtension'  => "",
                'threshold'      => 0
            ],
            'cachePath' => "../../storage/cache/root/",
            'sess' => [
                'driver'            => 'files',
                'cookieName'        => 'mta_cms_root_session',
                'samesite'          => 'Lax',
                'expiration'        => 7200,
                'matchIp'           => false,
                'timeToUpdate'      => 300,
                'regenerateDestroy' => false
            ],
            'cookie' => [
                'prefix'   => "",
                'domain'   => "",
                'path'     => "/",
                'secure'   => false,
                'httponly' => false,
                'samesite' => 'Lax'
            ],
            'csrf' => [
                'protection' => false,
                'tokenName'  => 'csrf_test_name',
                'cookieName' => 'csrf_cookie_name',
                'expire'     => 7200,
                'regenerate' => true,
                'excludeUris' => []
            ],
            'proxyIps' => "",
            'routes'   => []
        ];
    }

    /**
     * Get root directory path
     *
     * @return string
     */
    public function getRootDir()
    {
        return $this->rootDir;
    }

    /**
     * Get root path
     *
     * @return string
     */
    public function getRootPath()
    {
        return $this->rootPath;
    }

    /**
     * Get Config Path
     * 
     * @return string
     */
    protected function _getConfigPath()
    {
        // Assume config is a sibling of the 'root' folder's parent or within project server/config
        $frameworkParent = dirname($this->rootPath);
        $candidate = $frameworkParent . '/config/';
        
        if (is_dir($candidate)) {
            return $candidate;
        }

        return $this->rootDir . '/server/config/';
    }

    /**
     * Load Config File
     *
     * @param	string	$file           Configuration file name
     * @param	bool	$useSections   Whether config values should be loaded into their own section
     * @param	bool	$failGracefully Whether to halt execution if file is not found
     * @return	bool	true if the file was loaded correctly or false on failure
     */
    public function load($file = 'config', $useSections = false, $failGracefully = false)
    {
        $file = str_replace('.php', '', $file);
        $config_dir = $this->_getConfigPath();
        $file_path = $config_dir . $file . '.yaml';

        if (in_array($file_path, $this->isLoaded, true)) {
            return true;
        }

        if (!file_exists($file_path)) {
            if ($failGracefully === true) {
                return false;
            }
            trigger_error('The configuration file ' . $file . '.yaml does not exist in ' . $config_dir, E_USER_ERROR);
        }

        $yaml = new Yaml();
        $config = $yaml->parse($file_path);

        if (!is_array($config)) {
            if ($failGracefully === true) {
                return false;
            }
            trigger_error('Your ' . $file_path . ' file does not appear to contain a valid configuration array.', E_USER_ERROR);
        }

        if ($useSections === true) {
            $this->config[$file] = isset($this->config[$file])
                ? array_merge($this->config[$file], $config)
                : $config;
        } else {
            $this->config = array_merge($this->config, $config);
        }

        $this->isLoaded[] = $file_path;
        
        return true;
    }

    /**
     * Fetch a config file item (Getter Alias)
     *
     * @param	string	$item	Config item name
     * @param	string	$index	Index name
     * @return	mixed	The configuration item or null if the item doesn't exist
     */
    public function get($item, $index = '')
    {
        return $this->item($item, $index);
    }

    /**
     * Set a config file item and save to YAML file
     *
     * @param	string	$item	Config item name
     * @param	mixed	$value	Config item value
     * @param	string	$file	Configuration file name to save into
     * @return	bool	true on success, false on failure
     */
    public function set($item, $value, $file = 'config')
    {
        $file = str_replace('.php', '', $file);
        $file_path = $this->_getConfigPath() . $file . '.yaml';

        // Update in-memory flat config
        $this->config[$item] = $value;

        // If file is already loaded in a section, update that too
        if (isset($this->config[$file]) && is_array($this->config[$file])) {
            $this->config[$file][$item] = $value;
        }

        // To save accurately, we load the file data independently
        $yaml = new Yaml();
        $fileData = $yaml->parse($file_path);
        
        $fileData[$item] = $value;

        $result = $yaml->dump($file_path, $fileData);
        
        if ($result !== false) {
            error_log( 'Config file updated and saved: ' . $file_path);
            return true;
        }

        return false;
    }

    /**
     * Fetch a config file item
     *
     * @param	string	$item	Config item name
     * @param	string	$index	Index name
     * @return	mixed	The configuration item or null if the item doesn't exist
     */
    public function item($item, $index = '')
    {
        if ($index == '') {
            return isset($this->config[$item]) ? $this->config[$item] : null;
        }

        return isset($this->config[$index], $this->config[$index][$item]) ? $this->config[$index][$item] : null;
    }

    /**
     * Fetch a config file item with slash appended (if not empty)
     *
     * @param	string	$item	Config item name
     * @return	string|null	The configuration item or null if the item doesn't exist
     */
    public function slashItem($item)
    {
        if (!isset($this->config[$item])) {
            return null;
        } elseif (trim((string)$this->config[$item]) === '') {
            return '';
        }

        return rtrim((string)$this->config[$item], '/') . '/';
    }



    /**
     * Set a config file item (In-memory only)
     *
     * @param	string	$item	Config item name
     * @param	mixed	$value	Config item value
     * @return	void
     */
    public function setItem($item, $value)
    {
        $this->config[$item] = $value;
    }
}
