<?php

namespace Opifer\RulesEngineBundle\Provider;

use Opifer\RulesEngine\Environment\Environment;
use Opifer\RulesEngine\Rule\Rule;
use JMS\Serializer\Serializer;

/**
 * Base provider
 *
 * @author Opifer <info@opifer.nl>
 */
abstract class AbstractProvider
{
    protected $context;
    protected $environment;

    /**
     * Get environment
     *
     * @return Environment
     */
    public function getEnvironment()
    {
        if (null === $this->environment) {
            $this->environment = new Environment();
        }

        return $this->environment;
    }

    /**
     * Set context
     *
     * @param object $context
     */
    public function setContext($context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Evaluate rule
     *
     * @param  Rule $rule
     *
     * @return mixed
     */
    public function evaluate(Rule $rule)
    {
        $this->getEnvironment()->evaluate($rule);
    }

    /**
     * Get context
     *
     * @return object
     */
    public function getContext()
    {
        return $this->context;
    }
}
