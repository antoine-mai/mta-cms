<?php
defined('ADMIN_ROOT') OR exit('No direct script access allowed');
class DB_pdo_odbc_forge extends DB_pdo_forge {
	protected $_unsigned		= FALSE;
	protected function _attr_auto_increment(&$attributes, &$field)
	{
	}
}
