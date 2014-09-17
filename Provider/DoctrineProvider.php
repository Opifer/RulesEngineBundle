<?php

namespace Opifer\RulesEngineBundle\Provider;

use Doctrine\ORM\EntityManager;

use Opifer\RulesEngine\Environment\DoctrineEnvironment;
use Opifer\RulesEngine\Rule\Condition\AttributeCondition;
use Opifer\RulesEngine\Rule\Condition\Condition;
use Opifer\RulesEngine\Rule\Condition\ConditionSet;

class DoctrineProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * Constructor
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->environment = new DoctrineEnvironment();
    }

    /**
     * Build rules
     *
     * @return array
     */
    public function buildRules()
    {
        $rules = [];
        $rules[] = new ConditionSet();

        $entity = 'OpiferEavBundle:Template';

        $attributeCondition = new AttributeCondition();
        $attributeCondition->setName('Template: Name')
            ->setOperatorOpts(array('equals', 'notequals'))
            ->setEntity($entity)
            ->setAttribute('name');
        $rules[] = $attributeCondition;

        $attributeCondition = new AttributeCondition();
        $attributeCondition->setName('Template: Type')
            ->setOperatorOpts(array('equals', 'notequals'))
            ->setEntity($entity)
            ->setAttribute('type');
        $rules[] = $attributeCondition;

        $attributeCondition = new AttributeCondition();
        $attributeCondition->setName('Template: Abracadabrs')
            ->setOperatorOpts(array('equals', 'notequals'))
            ->setEntity($entity)
            ->setAttribute('type');
        $rules[] = $attributeCondition;

        return $rules;
    }

    /**
     * Evaluate the rule
     *
     * @param \Opifer\RulesEngine\Rule\Rule $rule
     *
     * @return Provider
     */
    public function evaluate($rule)
    {
        $repository = $this->em->getRepository($rule->getEntity());

        $this->environment->queryBuilder = $repository->createQueryBuilder('a');
        $this->environment->evaluate($rule);

        return $this;
    }

    /**
     * Get query results
     *
     * @param integer $limit
     *
     * @return ArrayCollection
     */
    public function getQueryResults($limit = null)
    {
        return $this->environment->getQueryResults($limit);
    }
}
