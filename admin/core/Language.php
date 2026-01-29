<?php namespace Admin\Core;

/**
 * Lang Class
 *
 * Handles language file loading and line retrieval.
 */
class Language
{
    /**
     * List of loaded language lines
     *
     * @var array
     */
    public $language = [];

    /**
     * List of loaded language files
     *
     * @var array
     */
    public $isLoaded = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        Error::logMessage('info', 'Language Class Initialized');
    }

    /**
     * Load a language file
     *
     * @param	mixed	$langfile	Language file name
     * @param	string	$idiom		Language name (e.g. english)
     * @param	bool	$return		Whether to return the loaded array of lines
     * @param	bool	$addSuffix	Whether to add the "_lang" suffix to the filename
     * @param	string	$altPath	Alternative path to look for the file
     * @return	mixed
     */
    public function load($langfile, $idiom = '', $return = false, $addSuffix = true, $altPath = '')
    {
        if (is_array($langfile)) {
            foreach ($langfile as $value) {
                $this->load($value, $idiom, $return, $addSuffix, $altPath);
            }
            return;
        }

        $langfile = str_replace('.php', '', (string)$langfile);
        if ($addSuffix === true) {
            $langfile = preg_replace('/_lang$/', '', $langfile) . '_lang';
        }
        $langfile .= '.php';

        if (empty($idiom) || !preg_match('/^[a-z_-]+$/i', (string)$idiom)) {
            $config = &Registry::getInstance('Config', 'core');
            $idiom = $config->item('language');
            if (empty($idiom)) {
                $idiom = 'english';
            }
        }

        if ($return === false && isset($this->isLoaded[$langfile]) && $this->isLoaded[$langfile] === $idiom) {
            return;
        }

        $basepath = ROOT_DIR . '/storage/language/admin/' . $idiom . '/' . $langfile;
        $found = false;

        if (file_exists($basepath)) {
            include($basepath);
            $found = true;
        }

        if ($altPath !== '') {
            $altPathFull = $altPath . 'language/' . $idiom . '/' . $langfile;
            if (file_exists($altPathFull)) {
                include($altPathFull);
                $found = true;
            }
        }

        if ($found !== true) {
            Error::showError('Unable to load the requested language file: storage/language/admin/' . $idiom . '/' . $langfile);
        }

        if (!isset($lang) || !is_array($lang)) {
            Error::logMessage('error', 'Language file contains no data: storage/language/admin/' . $idiom . '/' . $langfile);
            if ($return === true) {
                return [];
            }
            return;
        }

        if ($return === true) {
            return $lang;
        }

        $this->isLoaded[$langfile] = $idiom;
        $this->language = array_merge($this->language, $lang);
        Error::logMessage('info', 'Language file loaded: storage/language/admin/' . $idiom . '/' . $langfile);
        return true;
    }

    /**
     * Get a specific language line
     *
     * @param	string	$line		Language line key
     * @param	bool	$logErrors	Whether to log an error if the line isn't found
     * @return	string|bool
     */
    public function line($line, $logErrors = true)
    {
        $value = isset($this->language[$line]) ? $this->language[$line] : false;
        if ($value === false && $logErrors === true) {
            Error::logMessage('error', 'Could not find the language line "' . $line . '"');
        }
        return $value;
    }
}
