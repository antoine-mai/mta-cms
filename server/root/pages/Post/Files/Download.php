<?php namespace Root\Pages\Post\Files;
/**
 * 
 * @package 
 * @author  <[EMAIL_ADDRESS]>
 * @version 1.0.0
 * @license MIT
**/
use \Root\Core\Response\Response;
use \Root\Core\Request\Request;
use \Root\Core\Controller;
/**
 * 
**/
class Download extends Controller
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
            $tempZip = $fileManager->download($path);

            $zipName = basename($path) . '.zip';

            // Send the file
            header('Content-Description: File Transfer');
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . $zipName . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($tempZip));
            
            readfile($tempZip);
            
            // Cleanup
            unlink($tempZip);
            exit;
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 500;
            return $this->json([ 'success' => false, 'message' => $e->getMessage()], 200);
        }
    }
}
