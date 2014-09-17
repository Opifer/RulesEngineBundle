<?php

namespace Opifer\RulesEngineBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Opifer\RulesEngineBundle\DependencyInjection\Compiler\ProviderCompilerPass;

class OpiferRulesEngineBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ProviderCompilerPass());
    }
}
