<?php
defined('ADMIN_ROOT') OR exit('No direct script access allowed');
class Welcome extends Controller {
	public function index()
	{
		$this->load->view('welcome_message');
	}
}
