<?php

namespace Opifer\RulesEngineBundle\Provider;

interface ProviderInterface
{
    public function buildRules();

    public function evaluate($rule);
}
