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
        $this->rootDir = defined('ROOT_DIR') ? ROOT_DIR : dirname(__DIR__, 3);
        $this->rootPath = defined('ROOT_PATH') ? ROOT_PATH : $this->rootDir . '/server/root/';

        $this->load('root', false, true);
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
        $file_path = $this->rootDir . '/server/config/' . $file . '.yaml';

        if (in_array($file_path, $this->isLoaded, true)) {
            return true;
        }

        if (!file_exists($file_path)) {
            if ($failGracefully === true) {
                return false;
            }
            trigger_error('The configuration file ' . $file . '.yaml does not exist in ' . $this->rootDir . '/server/config/', E_USER_ERROR);
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
        
        if ($file !== 'config') {
        }

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
        $file_path = $this->rootDir . '/server/config/' . $file . '.yaml';

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
