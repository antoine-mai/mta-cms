<?php
/**
 * Global Helper Functions
 */

if (!function_exists('configItem')) {
    function configItem($item) {
        return \Root\Core\Common::configItem($item);
    }
}

if (!function_exists('isPhp')) {
    function isPhp($version) {
        return \Root\Core\Common::isPhp($version);
    }
}

if (!function_exists('htmlEscape')) {
    function htmlEscape($var, $doubleEncode = true) {
        return \Root\Core\Common::htmlEscape($var, $doubleEncode);
    }
}

if (!function_exists('removeInvisibleCharacters')) {
    function removeInvisibleCharacters($str, $urlEncoded = true) {
        return \Root\Core\Common::removeInvisibleCharacters($str, $urlEncoded);
    }
}
