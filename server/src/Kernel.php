<?php declare(strict_types=1); namespace App;
/**
 * 
**/
use \Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use \Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use \Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle;
use \Symfony\Component\HttpKernel\Kernel as BaseKernel;
use \Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use \Symfony\Bundle\MonologBundle\MonologBundle;
use \Symfony\Bundle\TwigBundle\TwigBundle;
use \Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
/**
 * 
**/
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
        yield new DoctrineBundle();
        yield new MonologBundle();
        yield new TwigBundle();

        if ('dev' === $this->getEnvironment()) {
            yield new WebProfilerBundle();
        }
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import(ROOT_DIR . '/config/*.yaml');
        $container->import(ROOT_DIR . '/config/packages/*.yaml');
        
        if (isset($this->bundles['WebProfilerBundle']))
        {
            $container->extension('web_profiler', [
                'toolbar' => true,
                'intercept_redirects' => false,
            ]);
        }
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        if (isset($this->bundles['WebProfilerBundle']))
        {
            $routes->import('@WebProfilerBundle/Resources/config/routing/wdt.php', 'php')->prefix('/_wdt');
            $routes->import('@WebProfilerBundle/Resources/config/routing/profiler.php', 'php')->prefix('/_profiler');
        }

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