<?php namespace Root\Pages\Post\Files;
/**
 * @author Antoine
 * @version 1.0
 * @package Root\Pages\Post\Files
 * @subpackage Rename
 * @category Controller
 * @license MIT
**/
use \Root\Core\Response\Response;
use \Root\Core\Request\Request;
use \Root\Core\Controller;

class Rename extends Controller
{
    public function index(Request $request): Response
    {
        try {
            $body = $request->getBody();
            $path = $body['path'] ?? '';
            $newName = $body['name'] ?? '';

            if (empty($path) || empty($newName)) {
                return $this->json([ 'success' => false, 'message' => 'Path and new name are required'],  200);
            }

            /** @var \Root\Services\FileManager $fileManager */
            $fileManager = \Root\Core\Registry::getInstance('FileManager');
            $fileManager->rename($path, $newName);

            return $this->json(['success' => true]);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            return $this->json([ 'success' => false, 'message' => $e->getMessage()], 200);
        }
    }
}
