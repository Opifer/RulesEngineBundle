<?php

namespace Opifer\RulesEngineBundle\Provider;

use Opifer\RulesEngine\Condition\ConditionSet;
use Opifer\RulesEngine\Operator\Logical\Equals;
use Opifer\RulesEngine\RulesEngine;

/**
 * Logical Provider
 */
class LogicalProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function evaluate(ConditionSet $set)
    {
        $rulesEngine = new RulesEngine();
        $rulesEngine->interpret($set);

        return $context->getData();
    }

    /**
     * {@inheritDoc}
     */
    public function getOperators()
    {
        return [
            'Equals' => new Equals()
        ];
    }
}
