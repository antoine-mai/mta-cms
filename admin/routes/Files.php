<?php namespace Admin\Routes;
/**
 * 
**/
use Admin\Core\Route;
/**
 * 
**/
use Admin\Core\Request\Request;
use Admin\Core\Response\Response;

/**
 * 
**/
class Files extends Route
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