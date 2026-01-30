<?php namespace Root\Pages\Post\Files;
/**
 * @author Antoine
 * @version 1.0
 * @package Root\Pages\Post\Files
 * @subpackage Upload
 * @category Controller
 * @license MIT
**/
use \Root\Core\Response\Response;
use \Root\Core\Request\Request;
use \Root\Core\Controller;

class Upload extends Controller
{
    public function index(Request $request): Response
    {
        try {
            $path = $_POST['path'] ?? '/';

            if (!isset($_FILES['file'])) {
                return $this->json([ 'success' => false, 'message' => 'No file uploaded'],  200);
            }

            /** @var \Root\Services\FileManager $fileManager */
            $fileManager = \Root\Core\Registry::getInstance('FileManager');
            $fileManager->upload($path, $_FILES['file']);

            return $this->json(['success' => true]);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            return $this->json([ 'success' => false, 'message' => $e->getMessage()], 200);
        }
    }
}
