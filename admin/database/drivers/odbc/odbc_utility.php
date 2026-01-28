<?php
defined('ADMIN_ROOT') OR exit('No direct script access allowed');
class DB_odbc_utility extends DB_utility {
	protected function _backup($params = array())
	{
		return $this->db->display_error('db_unsupported_feature');
	}
}
