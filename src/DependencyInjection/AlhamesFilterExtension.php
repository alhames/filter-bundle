<?php

namespace Alhames\FilterBundle\DependencyInjection;

use Alhames\FilterBundle\Filter\FilterInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class AlhamesFilterExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $fileLocator = new FileLocator(__DIR__.'/../../config');
        $loader = new Loader\YamlFileLoader($container, $fileLocator);
        $loader->load('services.yaml');

        $container->registerForAutoconfiguration(FilterInterface::class)
            ->addTag('alhames_filter');
    }
}
