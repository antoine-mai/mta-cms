<?php declare(strict_types=1); namespace App;
/**
 * 
**/
use \Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use \Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use \Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use \Symfony\Component\HttpKernel\Kernel as BaseKernel;
use \Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use \Symfony\Bundle\MonologBundle\MonologBundle;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;
    
    public function getProjectDir(): string
    {
        return ROOT_DIR;
    }

    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
        yield new MonologBundle();
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import(ROOT_DIR . '/server/config/framework.yaml');
        $container->import(ROOT_DIR . '/server/config/services.yaml');
        $container->import(ROOT_DIR . '/server/config/packages/*.yaml');
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        // 1. Import Main/Public Controllers ONLY (Admin is handled by Legacy Bridge in startup.php)
        $routes->import(__DIR__ . '/Controller/HomeController.php', 'attribute');
    }

    public function getCacheDir(): string
    {
        return ROOT_DIR . '/storage/cache/' . $this->getEnvironment();
    }

    public function getLogDir(): string
    {
        return ROOT_DIR . '/storage/log/' . $this->getEnvironment();
    }
}