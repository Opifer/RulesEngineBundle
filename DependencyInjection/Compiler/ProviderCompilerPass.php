<?php

namespace Opifer\RulesEngineBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class ProviderCompilerPass implements CompilerPassInterface
{
    /**
     * Process the compiler pass
     *
     * Adds all tagged provider services to the provider pool
     *
     * @param Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('opifer.rulesengine.provider.pool')) {
            return;
        }

        $definition = $container->getDefinition('opifer.rulesengine.provider.pool');
        $taggedServices = $container->findTaggedServiceIds('opifer.rulesengine.provider');

        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall('addProvider', [new Reference($id), $attributes['alias']]);
            }
        }
    }
}
