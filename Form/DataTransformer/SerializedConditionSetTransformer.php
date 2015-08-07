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
    /** @var RulesEngine */
    protected $rulesEngine;

    /**
     * @param RulesEngine $rulesEngine
     */
    public function __construct(RulesEngine $rulesEngine)
    {
        $this->rulesEngine = $rulesEngine;
    }

    /**
     * {@inheritDoc}
     */
    public function transform($json)
    {
        if (null === $json || !is_string($json)) {
            return new ConditionSet();
        }

        try {
            $serialized = $this->rulesEngine->deserialize($json);
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

        return $this->rulesEngine->serialize($conditionSet);
    }
}
