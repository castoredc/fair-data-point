<?php
declare(strict_types=1);

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use function dirname;
use function is_file;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    /** @inheritDoc */
    public function getCacheDir()
    {
        return $this->getProjectDir() . '/var/cache/' . $this->environment;
    }

    /** @inheritDoc */
    public function getLogDir()
    {
        return $this->getProjectDir() . '/var/log';
    }

    /** @inheritDoc */
    public function registerBundles()
    {
        $contents = require $this->getProjectDir() . '/config/bundles.php';
        foreach ($contents as $class => $envs) {
            if (! isset($envs['all']) && ! isset($envs[$this->environment])) {
                continue;
            }

            yield new $class();
        }
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        // Feel free to remove the "container.autowiring.strict_mode" parameter
        // if you are using symfony/dependency-injection 4.0+ as it's the default behavior
        $container->setParameter('container.autowiring.strict_mode', true);
        $container->setParameter('container.dumper.inline_class_loader', true);
        $confDir = $this->getProjectDir() . '/config';

        $loader->load($confDir . '/{packages}/*' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{packages}/' . $this->environment . '/**/*' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{services}' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{services}_' . $this->environment . self::CONFIG_EXTS, 'glob');
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import('../config/{routes}/' . $this->environment . '/*.yaml');
        $routes->import('../config/{routes}/*.yaml');

        if (is_file(dirname(__DIR__) . '/config/routes.yaml')) {
            $routes->import('../config/{routes}.yaml');
        } elseif (is_file(dirname(__DIR__) . '/config/routes.php')) {
            $path = dirname(__DIR__) . '/config/routes.php';
            (require $path)($routes->withPath($path), $this);
        }
    }
}
