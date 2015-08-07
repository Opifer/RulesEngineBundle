<?php

namespace Opifer\RulesEngineBundle\Provider;

use Opifer\RulesEngine\Condition\ConditionSet;
use Opifer\RulesEngine\Context\Context;
use Opifer\RulesEngine\RulesEngine;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractProvider implements ProviderInterface
{
    /** @var RulesEngine */
    protected $rulesEngine;

    /**
     * Constructor
     *
     * @param RulesEngine $rulesEngine
     */
    public function __construct(RulesEngine $rulesEngine)
    {
        $this->rulesEngine = $rulesEngine;
    }

    /**
     * {@inheritDoc}
     */
    public function evaluate(ConditionSet $set)
    {
        $context = new Context();

        $this->rulesEngine->interpret($set, $context);

        return $context->getData();
    }

    /**
     * {@inheritDoc}
     */
    public function getLefts()
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getOperators()
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function getRights()
    {
        return null;
    }
}
