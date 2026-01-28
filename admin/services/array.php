<?php
defined('ADMIN_ROOT') OR exit('No direct script access allowed');
if ( ! function_exists('element'))
{
	function element($item, array $array, $default = NULL)
	{
		return array_key_exists($item, $array) ? $array[$item] : $default;
	}
}
if ( ! function_exists('random_element'))
{
	function random_element($array)
	{
		return is_array($array) ? $array[array_rand($array)] : $array;
	}
}
if ( ! function_exists('elements'))
{
	function elements($items, array $array, $default = NULL)
	{
		$return = [];
		is_array($items) OR $items = [$items];
		foreach ($items as $item)
		{
			$return[$item] = array_key_exists($item, $array) ? $array[$item] : $default;
		}
		return $return;
	}
}
