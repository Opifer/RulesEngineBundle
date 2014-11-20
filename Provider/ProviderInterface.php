<?php

namespace Opifer\RulesEngineBundle\Provider;

use Opifer\RulesEngine\Rule\Rule;

/**
 * Rulesengine provider interface
 */
interface ProviderInterface
{
    /**
     * Declares what rules are allowed in the rulesengine
     *
     * @return array
     */
    public function buildRules();

    /**
     * Performs the rule evaluation
     *
     * @param  Rule   $rule
     *
     * @return mixed
     */
    public function evaluate(Rule $rule);
}
