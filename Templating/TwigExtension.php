<?php

namespace Opifer\RulesEngineBundle\Templating;

use Opifer\RulesEngineBundle\Provider\DoctrineProvider;

/**
 * @todo Make this independent from the DoctrineProvider
 */
class TwigExtension extends \Twig_Extension
{
    /**
     * @var DoctrineProvider
     */
    protected $rulesEngineProvider;

    /**
     * Constructor
     *
     * @param DoctrineProvider $rulesEngineProvider
     */
    public function __construct(DoctrineProvider $rulesEngineProvider)
    {
        $this->rulesEngineProvider = $rulesEngineProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('evaluate_rule', [$this, 'evaluateRule']),
        ];
    }

    /**
     * Get the view for the placeholder
     *
     * @param string $rule
     *
     * @return string
     */
    public function evaluateRule($rule, $limit = null)
    {
        return $this->rulesEngineProvider->evaluate($rule)->getQueryResults($limit);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'opifer.rules_engine.templating.twig_extension';
    }
}
