<?php
defined('ADMIN_ROOT') OR exit('No direct script access allowed');
class DB_sqlsrv_utility extends DB_utility {
	protected $_list_databases	= 'EXEC sp_helpdb'; // Can also be: EXEC sp_databases
	protected $_optimize_table	= 'ALTER INDEX all ON %s REORGANIZE';
	protected function _backup($params = array())
	{
		return $this->db->display_error('db_unsupported_feature');
	}
}
