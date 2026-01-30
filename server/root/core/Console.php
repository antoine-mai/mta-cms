<?php namespace Root\Core;
/**
 * Console Class
 *
 * @package		Root\Core
 * @category	Console
 * @author		Antoine
**/
class Console
{
    /**
     * Is CLI?
     *
     * Test to see if a request was made from the command line.
     *
     * @return 	bool
     */
    public static function isCli()
    {
        return (PHP_SAPI === 'cli' OR defined('STDIN'));
    }
}
