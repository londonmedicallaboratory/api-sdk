<?php

declare(strict_types=1);

namespace LML\SDK\Tests;

use LML\SDK\LMLSDKBundle;
use LML\View\LMLViewBundle;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use function str_starts_with;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class TestKernel extends Kernel implements CompilerPassInterface
{
    use MicroKernelTrait;

    public function __construct()
    {
        parent::__construct('test', true);
    }

    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
        yield new SecurityBundle();
        yield new LMLSDKBundle();
        yield new LMLViewBundle();
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/config/test_config.yaml');
    }

    public function process(ContainerBuilder $container): void
    {
        foreach ($container->getDefinitions() as $id => $definition) {
            // all services within the bundle will become public
            if (str_starts_with($id, 'lml_sdk') || str_starts_with($id, 'LML\SDK')) {
                $definition->setPublic(true);
            }
        }
    }
}
