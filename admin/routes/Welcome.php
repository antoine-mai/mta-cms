<?php namespace Admin\Routes;
/**
 * 
**/
use Admin\Core\Route;
/**
 * 
**/
class Welcome extends Route {
	public function index()
	{
		$this->load->template('welcome_message');
	}

	public function post()
	{
		$this->load->template('welcome_message');
	}
}
