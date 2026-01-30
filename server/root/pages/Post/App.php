<?php namespace Root\Pages\Post;

use Root\Core\Controller;
use Root\Core\Request\Request;
use Root\Core\Response\Response;

class App extends Controller
{
    /**
     * Get list of installed apps
     */
    public function index(Request $request): Response
    {
        $apps = [
            [
                'id' => '0',
                'name' => 'User Dashboard',
                'description' => 'Quick access to user statistics, recent activities, and management shortcuts.',
                'icon' => 'User',
                'status' => 'active',
                'version' => '1.0.0',
                'category' => 'General',
                'author' => 'MTA Team'
            ],
            [
                'id' => '5',
                'name' => 'CMS',
                'description' => 'Centralized content management system for pages, posts, and media.',
                'icon' => 'Layout',
                'status' => 'active',
                'version' => '1.2.0',
                'category' => 'Core',
                'author' => 'MTA Team'
            ],
            [
                'id' => '6',
                'name' => 'Ecommerce',
                'description' => 'Full-featured online store management with products, orders, and payments.',
                'icon' => 'ShoppingCart',
                'status' => 'active',
                'version' => '2.1.0',
                'category' => 'Commerce',
                'author' => 'MTA Team'
            ]
        ];

        return $this->json($apps);
    }

    /**
     * Install an app (placeholder)
     */
    public function install(Request $request): Response
    {
        return $this->json(['success' => true, 'message' => 'App installation started.']);
    }

    /**
     * Uninstall an app (placeholder)
     */
    public function uninstall(Request $request): Response
    {
        $id = $request->get('id');
        return $this->json(['success' => true, 'message' => "App $id uninstalled."]);
    }
}
