<?php

namespace Opifer\RulesEngineBundle\Provider;

use Opifer\RulesEngine\Condition\ConditionSet;

/**
 * Rulesengine provider interface
 */
interface ProviderInterface
{
    public function evaluate(ConditionSet $set);

    /**
     * @return null|array
     */
    public function getLefts();

    /**
     * @return null|array
     */
    public function getOperators();

    /**
     * @return null|array
     */
    public function getRights();
}
