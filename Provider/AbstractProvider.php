<?php

namespace Opifer\RulesEngineBundle\Provider;

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
        return $this->environment;
    }

    public function evaluate($rule)
    {
        $this->environment->evaluate($rule);
    }

    public function getContext()
    {
        return $this->context;
    }

    public function setContext($context)
    {
        $this->context = $context;

        return $this;
    }
}
