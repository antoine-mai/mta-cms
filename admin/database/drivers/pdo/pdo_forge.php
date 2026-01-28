<?php
defined('ADMIN_ROOT') OR exit('No direct script access allowed');
class DB_pdo_forge extends DB_forge {
	protected $_create_table_if	= FALSE;
	protected $_drop_table_if	= FALSE;
}
