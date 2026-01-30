<?php namespace Root\Pages\Post\Files;

/**
 * 
 * @package 
 * @author  <[EMAIL_ADDRESS]>
 * @version 1.0.0
 * @license MIT
 */
use \Root\Core\Response\Response;
use \Root\Core\Request\Request;
use \Root\Core\Controller;

/**
 * 
**/
class Create extends Controller
{
    public function index(Request $request): Response
    {
        try {
            $data = $request->getBody();
            $parentPath = $data['path'] ?? '/';
            $name = $data['name'] ?? null;
            $type = $data['type'] ?? 'file';

            if (!$name) {
                return $this->json([ 'success' => false, 'message' => 'Name is required'],  200);
            }

            /** @var \Root\Services\FileManager $fileManager */
            $fileManager = \Root\Core\Registry::getInstance('FileManager');
            $fileManager->create($parentPath, $name, $type);

            return $this->json(['success' => true, 'path' => rtrim($parentPath, '/') . '/' . $name]);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            return $this->json([ 'success' => false, 'message' => $e->getMessage()], 200);
        }
    }
}
