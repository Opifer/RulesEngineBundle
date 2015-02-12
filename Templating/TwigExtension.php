<?php

namespace Opifer\RulesEngineBundle\Templating;

use Opifer\RulesEngineBundle\Provider\ProviderInterface;
use Opifer\EavBundle\Entity\QueryValue;
use Opifer\RulesEngine\Rule\Rule;

/**
 * @todo Make this independent from the ProviderInterface
 */
class TwigExtension extends \Twig_Extension
{
    /**
     * @var ProviderInterface
     */
    protected $rulesEngineProvider;

    /**
     * Constructor
     *
     * @param ProviderInterface $rulesEngineProvider
     */
    public function __construct(ProviderInterface $rulesEngineProvider)
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
            new \Twig_SimpleFunction('evaluate_query_rule', [$this, 'evaluateQueryRule']),
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
     * Evaluate rule with query parameters
     * 
     * @param QueryValue $query
     * @param array $params
     * @return mixed
     */
    public function evaluateQueryRule(QueryValue $query, $params = [])
    {
        if(($rule = $query->getRule()) instanceof Rule) {
            $environment = $this->rulesEngineProvider->evaluate($rule);
            $this->rulesEngineProvider->setQueryParams($query->getId(), $params);
            
            return $environment->getQueryResults();
        }
        
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'opifer.rules_engine.templating.twig_extension';
    }
}
