<?php namespace Root\Pages\Post\Files;

use \Root\Core\Response\Response;
use \Root\Core\Request\Request;
use \Root\Core\Controller;

class Move extends Controller
{
    public function index(Request $request): Response
    {
        try {
            $body = $request->getBody();
            $path = $body['path'] ?? '';
            $destination = $body['destination'] ?? '';

            if (!$path || !$destination) {
                return $this->json([ 'success' => false, 'message' => 'Source path and destination are required'],  200);
            }

            /** @var \Root\Services\FileManager $fileManager */
            $fileManager = \Root\Core\Registry::getInstance('FileManager');
            $fileManager->move($path, $destination);

            return $this->json(['success' => true]);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            return $this->json([ 'success' => false, 'message' => $e->getMessage()], 200);
        }
    }
}
