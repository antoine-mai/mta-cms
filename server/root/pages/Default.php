<?php namespace Root\Pages;
/**
 * 
**/
use \Root\Core\Response\Response;
use \Root\Core\Request\Request;
use \Root\Core\Controller;
/**
 * 
**/
class DefaultPage extends Controller
{
    public function index(Request $request): Response
    {
		$folderName = basename(dirname($_SERVER['SCRIPT_NAME'] ?? 'root'));
		$distPath = $this->config->getRootDir() . '/public/' . $folderName . '/dist/root.html';

		if (file_exists($distPath)) {
			$content = file_get_contents($distPath);
			$response = new Response();
			$response->setContent($content);
			return $response;
		}

		$response = new Response();
		$response->setContent('React App not built. Please run build script.');
        return $response;
	}

	public function post(Request $request): Response
	{
		return $this->json([
			'message' => 'Hello World'
		]);
	}
}