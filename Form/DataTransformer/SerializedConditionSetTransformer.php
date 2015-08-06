<?php

namespace Opifer\RulesEngineBundle\Form\DataTransformer;

use Opifer\RulesEngine\Condition\ConditionSet;
use Opifer\RulesEngine\RulesEngine;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Transforming conditionsets to serialized strings and viceversa
 */
class SerializedConditionSetTransformer implements DataTransformerInterface
{
    /**
     * {@inheritDoc}
     */
    public function transform($json)
    {
        if (null === $json || !is_string($json)) {
            return new ConditionSet();
        }

        $rulesEngine = new RulesEngine();

        try {
            $serialized = $rulesEngine->deserialize($json);
        } catch(\Exception $e) {
            return new ConditionSet();
        }

        return $serialized;
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($conditionSet)
    {
        if (!$conditionSet instanceof ConditionSet) {
            return;
        }

        $rulesEngine = new RulesEngine();

        return $rulesEngine->serialize($conditionSet);
    }
}
