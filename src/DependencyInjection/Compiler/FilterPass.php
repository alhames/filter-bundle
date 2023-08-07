<?php

namespace Alhames\FilterBundle\DependencyInjection\Compiler;

use Alhames\FilterBundle\Filter\FilterInterface;
use Alhames\FilterBundle\FilterManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class FilterPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $filters = $container->findTaggedServiceIds('alhames_filter');
        $manager = $container->getDefinition(FilterManager::class);

        foreach ($filters as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                /** @var FilterInterface $class */
                $class = $container->getDefinition($id)->getClass();
                $manager->addMethodCall('setFilter', [
                    '$type' => $class::getType(),
                    '$filter' => new Reference($id),
                ]);
            }
        }
    }
}
