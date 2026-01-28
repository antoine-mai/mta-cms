<?php declare(strict_types=1); namespace App\Controller;
/**
 * 
**/
use \Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use \Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\Routing\Attribute\Route;
/**
 * 
**/
class HomeController extends AbstractController
{
    #[Route('/{reactRouting}', name: 'index', requirements: ['reactRouting' => '^(?!api|_profiler|_wdt|assets|favicon).*'], defaults: ['reactRouting' => null])]
    public function index(): Response
    {
        $indexPath = $this->getParameter('kernel.project_dir') . '/public/index.html';
        
        if (!file_exists($indexPath)) {
            return new Response(
                "React build not found. Please run 'npm run build' in the client directory.",
                Response::HTTP_NOT_FOUND
            );
        }

        return new Response(file_get_contents($indexPath));
    }
}