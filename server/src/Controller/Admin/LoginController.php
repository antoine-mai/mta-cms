<?php declare(strict_types=1); namespace App\Controller\Admin;
/**
 * 
**/
use \Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use \Symfony\Component\HttpFoundation\RedirectResponse;
use \Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\Routing\Attribute\Route;
use \Symfony\Component\HttpFoundation\Request;
/**
 * 
**/
class LoginController extends AbstractController
{
    #[Route('/login', name: 'admin_login', methods: ['GET', 'POST'])]
    public function login(Request $request): Response
    {
        $session = $request->getSession();
        
        if ($session->get('admin_logged_in')) {
            return $this->redirectToRoute('admin_dashboard');
        }

        $error = null;

        if ($request->isMethod('POST')) {
            $user = $request->request->get('username');
            $pass = $request->request->get('password');

            $expectedUser = $_ENV['ADMIN_USER'] ?? 'admin';
            $expectedPass = $_ENV['ADMIN_PASS'] ?? 'admin';

            if ($user === $expectedUser && $pass === $expectedPass) {
                $session->set('admin_logged_in', true);
                $session->set('admin_user', $user);
                return $this->redirectToRoute('admin_dashboard');
            }

            $error = 'Invalid credentials';
        }

        return $this->render('admin/login.html.twig', [
            'error' => $error
        ]);
    }

    #[Route('/logout', name: 'admin_logout')]
    public function logout(Request $request): RedirectResponse
    {
        $request->getSession()->remove('admin_logged_in');
        $request->getSession()->remove('admin_user');
        return $this->redirectToRoute('admin_login');
    }
}
