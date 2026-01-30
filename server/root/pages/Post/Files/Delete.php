<?php namespace Root\Pages\Post\Files;
/**
 * @author Antoine
 * @version 1.0
 * @package Root\Pages\Post\Files
 * @subpackage Delete
 * @category Controller
 * @license MIT
 * @copyright 2026 Antoine
**/
use \Root\Core\Response\Response;
use \Root\Core\Request\Request;
use \Root\Core\Controller;

class Delete extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        try {
            $data = $request->getBody();
            $path = $data['path'] ?? null;

            if (!$path || $path === '/' || $path === '.') {
                return $this->json([ 'success' => false, 'message' => 'Invalid or missing path'],  200);
            }

            /** @var \Root\Services\FileManager $fileManager */
            $fileManager = \Root\Core\Registry::getInstance('FileManager');
            $fileManager->delete($path);

            return $this->json(['success' => true]);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            return $this->json([ 'success' => false, 'message' => $e->getMessage()], 200);
        }
    }
}
