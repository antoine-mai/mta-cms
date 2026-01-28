<?php
defined('ADMIN_ROOT') OR exit('No direct script access allowed');
class Model {
	public function __construct() {}
	public function __get($key)
	{
		return get_instance()->$key;
	}
}
