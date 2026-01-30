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
class Browse extends Controller
{
    public function index(Request $request): Response
    {
        try {
            /** @var \Root\Services\FileManager $fileManager */
            $fileManager = \Root\Core\Registry::getInstance('FileManager');
            $path = $request->query->get('path', '/');
            $items = $fileManager->browse($path);
            return $this->json(['items' => $items]);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            return $this->json([ 'success' => false, 'message' => $e->getMessage()], 200);
        }
    }
}
