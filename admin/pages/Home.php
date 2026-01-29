<?php namespace Admin\Pages;
/**
 * 
**/
use Admin\Core\Response\Response;
use Admin\Core\Request\Request;
use Admin\Core\Controller;
/**
 * 
**/
class Home extends Controller
{
	public function index(Request $request): Response
	{
		return $this->render('home');
	}

	public function post(Request $request): Response
	{
		return $this->json([
			'message' => 'Hello World'
		]);
	}
}