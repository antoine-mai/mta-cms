<?php declare(strict_types=1); namespace App\Controller\Admin;
/**
 * 
**/
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
/**
 *  
**/
class IndexController extends AdminController
{
    #[Route('', name: 'admin_dashboard')]
    public function index(Request $request): Response
    {
        // Enforce private access
        if (!$request->getSession()->get('admin_logged_in')) {
            return $this->redirectToRoute('admin_login');
        }

        return $this->renderAdmin('admin/index.html.twig');
    }

    #[Route('/settings', name: 'admin_settings')]
    public function settings(Request $request): Response
    {
        if (!$request->getSession()->get('admin_logged_in')) {
            return $this->redirectToRoute('admin_login');
        }
        
        return new Response('<h1>Admin Settings</h1><p>Welcome to the server-side settings page.</p>');
    }
}
