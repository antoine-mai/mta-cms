<?php namespace Root\Core;
/**
 * Language Class
 *
 * This class contains functions that enable config files to be managed.
 **/
class Language
{
    /**
     * List of all loaded language strings
     *
     * @var array
     */
    public $language = [];

    /**
     * List of all loaded language files
     *
     * @var array
     */
    public $isLoaded = [];

    /**
     * Selected idiom
     *
     * @var string
     */
    public $idiom;

    /**
     * Constructor
     */
    public function __construct()
    {
        $config = &Registry::getInstance('Config');
        $this->idiom = $config->item('language') ?? 'english';
    }

    /**
     * Load a language file
     *
     * @param	mixed	$langfile	Language file name
     * @param	string	$idiom		Language idiom (english, etc.)
     * @param	bool	$return		Whether to return the loaded array of translations
     * @param	bool	$add_suffix	Whether to add suffix to $langfile
     * @param 	string 	$alt_path 	Alternative path to look for the language file
     *
     * @return	void|string[]	Array of translations if $return is set to true
     */
    public function load($langfile, $idiom = '', $return = false, $add_suffix = true, $alt_path = '')
    {
        if (is_array($langfile)) {
            foreach ($langfile as $value) {
                $this->load($value, $idiom, $return, $add_suffix, $alt_path);
            }
            return;
        }

        $langfile = str_replace('.php', '', $langfile);

        if ($add_suffix === true) {
            $langfile = str_replace('_lang.', '', $langfile) . '_lang';
        }

        $langfile .= '.php';

        if (empty($idiom)) {
            $idiom = $this->idiom;
        }

        if (empty($alt_path)) {
            $config = &Registry::getInstance('Config');
            $alt_path = $config->getRootPath();
        }

        if (in_array($langfile, $this->isLoaded, true)) {
            return;
        }

        $file_path = $alt_path . 'language/' . $idiom . '/' . $langfile;

        if (!file_exists($file_path)) {
            $file_path = dirname($alt_path) . '/storage/language/root/' . $idiom . '/' . $langfile;
        }

        if (!file_exists($file_path)) {
            trigger_error('Unable to load the requested language file: language/' . $idiom . '/' . $langfile, E_USER_ERROR);
        }

        include($file_path);

        if (!isset($lang) || !is_array($lang)) {
            return;
        }

        if ($return === true) {
            return $lang;
        }

        $this->isLoaded[] = $langfile;
        $this->language = array_merge($this->language, $lang);

        return true;
    }

    /**
     * Language line
     *
     * Fetches a single line of text from the language array
     *
     * @param	string	$line		Language line key
     * @param	bool	$log_errors	Whether to log an error message if the line is not found
     * @return	string	Translation
     */
    public function line($line, $log_errors = true)
    {
        $value = isset($this->language[$line]) ? $this->language[$line] : false;

        if ($value === false && $log_errors === true) {
            // error_log( 'Could not find the language line "' . $line . '"');
        }

        return $value;
    }
}
