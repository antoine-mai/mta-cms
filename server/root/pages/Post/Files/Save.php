<?php namespace Root\Pages\Post\Files;
/**
 * @author Antoine
 * @version 1.0
 * @package Root\Pages\Post\Files
 * @subpackage Save
 * @category Controller
 * @license MIT
**/
use \Root\Core\Response\Response;
use \Root\Core\Request\Request;
use \Root\Core\Controller;
/**
 * 
**/
class Save extends Controller
{
    public function index(Request $request): Response
    {
        try {
            $body = $request->getBody();
            $path = $body['path'] ?? '';
            $content = $body['content'] ?? '';

            if (empty($path)) {
                return $this->json([ 'success' => false, 'message' => 'Path is required'],  200);
            }

            /** @var \Root\Services\FileManager $fileManager */
            $fileManager = \Root\Core\Registry::getInstance('FileManager');
            $fileManager->save($path, $content);

            return $this->json(['success' => true]);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            return $this->json([ 'success' => false, 'message' => $e->getMessage()], 200);
        }
    }
}
