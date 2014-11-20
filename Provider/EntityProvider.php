<?php

namespace Opifer\RulesEngineBundle\Provider;

use Doctrine\ORM\EntityManager;
use Opifer\CrudBundle\Doctrine\EntityHelper;
use Opifer\RulesEngine\Environment\DoctrineEnvironment;
use Opifer\RulesEngine\Rule\Condition\AttributeCondition;
use Opifer\RulesEngine\Rule\Condition\RelationCondition;
use Opifer\RulesEngine\Rule\Condition\Condition;
use Opifer\RulesEngine\Rule\Rule;

/**
 * Entity Provider
 */
class EntityProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * @var EntityHelper
     */
    protected $entityHelper;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * Constructor
     *
     * @param EntityHelper  $entityHelper
     * @param EntityManager $em
     */
    public function __construct(EntityHelper $entityHelper, EntityManager $em)
    {
        $this->entityHelper = $entityHelper;
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public function buildRules()
    {
        $rules = array();

        foreach ($this->entityHelper->getProperties($this->context) as $property) {
            $condition = new AttributeCondition();
            $condition
                ->setName(ucfirst($property['fieldName']))
                ->setEntity($this->context)
                ->setAttribute($property['fieldName'])
                ->setType($property['type']);

            $rules[] = $condition;
        }

        foreach ($this->entityHelper->getRelations($this->context) as $key => $relation) {
            foreach ($this->entityHelper->getProperties($relation['targetEntity']) as $relProperty) {
                $condition = new RelationCondition();
                $condition
                    ->setName(ucfirst($key) . ' ' .ucfirst($relProperty['fieldName']))
                    ->setRelation($key)
                    ->setEntity($relation['targetEntity'])
                    ->setAttribute($relProperty['fieldName'])
                    ->setType($relProperty['type']);

                $rules[] = $condition;
            }
            
        }

        return $rules;
    }

    /**
     * {@inheritDoc}
     */
    public function evaluate(Rule $rule)
    {
        // use exotic alias because we use entity's own repository
        $qb = $this->em->getRepository($rule->getEntity())
            ->createQueryBuilder('a');

        $environment = new DoctrineEnvironment();
        $environment->queryBuilder = $qb;

        return $environment->evaluate($rule);
    }
}
