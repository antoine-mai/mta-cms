<?php declare(strict_types=1); namespace App\Controller\Admin;
/**
 *  
**/
use \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use \Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use \Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\HttpFoundation\Request;
/**
 *  
**/
abstract class AdminController extends AbstractController
{
    /**
     * This method is called by Symfony after the controller is initialized.
     * We use it here to enforce authentication for all child controllers.
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            // Add any common services for admin here
        ]);
    }

    protected function checkAuth(Request $request): void
    {
        if (!$request->getSession()->get('admin_logged_in')) {
            // We'll handle redirection in the specific actions or via a listener 
            // but since this is a Base class, we provide the logic.
            throw new AccessDeniedHttpException('Admin access required');
        }
    }

    protected function renderAdmin(string $view, array $parameters = [], ?Response $response = null): Response
    {
        $session = $this->container->get('request_stack')->getCurrentRequest()->getSession();
        $parameters['admin_user'] = $session->get('admin_user') ?? $_ENV['ADMIN_USER'] ?? 'Guest';
        $parameters['admin_path'] = $_ENV['ADMIN_PATH'] ?? '/admin';
        
        return $this->render($view, $parameters, $response);
    }
}
