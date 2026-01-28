<?php declare(strict_types=1); namespace App;
/**
 * 
**/
require_once __DIR__ . '/vendor/autoload.php';
/**
 * 
**/
final class Startup
{
    public static function run(): void
    {
        if (file_exists(ROOT_DIR . '/.env')) {
            (new \Symfony\Component\Dotenv\Dotenv())->load(ROOT_DIR . '/.env');
        }

        $env = $_ENV['APP_ENV'] ?? 'dev';
        $debug = (bool) ($_ENV['APP_DEBUG'] ?? ('prod' !== $env));

        $kernel = new \App\Kernel($env, $debug);
        $request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
        $response = $kernel->handle($request);
        $response->send();
        $kernel->terminate($request, $response);
    }
}