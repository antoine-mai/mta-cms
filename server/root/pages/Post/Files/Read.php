<?php namespace Root\Pages\Post\Files;
/**
 * @author Antoine
 * @version 1.0
 * @package Root\Pages\Post\Files
 * @subpackage Read
 * @category Controller
 * @license MIT
**/
use \Root\Core\Response\Response;
use \Root\Core\Request\Request;
use \Root\Core\Controller;

class Read extends Controller
{
    public function index(Request $request): Response
    {
        try {
            $path = $request->query->get('path', '');

            if (empty($path)) {
                return $this->json([ 'success' => false, 'message' => 'Path is required'],  200);
            }

            /** @var \Root\Services\FileManager $fileManager */
            $fileManager = \Root\Core\Registry::getInstance('FileManager');
            $content = $fileManager->read($path);

            return $this->json(['content' => $content]);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            return $this->json([ 'success' => false, 'message' => $e->getMessage()], 200);
        }
    }
}
