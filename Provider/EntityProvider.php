<?php

namespace Opifer\RulesEngineBundle\Provider;

use Doctrine\ORM\EntityManager;

use Opifer\CrudBundle\Doctrine\EntityHelper;
use Opifer\RulesEngine\Rule\Condition\AttributeCondition;
use Opifer\RulesEngine\Rule\Condition\RelationCondition;
use Opifer\RulesEngine\Rule\Condition\Condition;

class EntityProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * @var EntityHelper
     */
    private $entityHelper;

    /**
     * Constructor
     *
     * @param EntityHelper $entityHelper
     */
    public function __construct(EntityHelper $entityHelper)
    {
        $this->entityHelper = $entityHelper;
    }

    /**
     * Build rules
     *
     * @return array
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
                ->setType($property['type'])
            ;

            $rules[] = $condition;
        }

        foreach ($this->entityHelper->getRelations($this->context) as $key => $relation) {
            foreach ($this->entityHelper->getProperties($relation['targetEntity']) as $relProperty) {
                $condition = new RelationCondition();
                $condition->setName(ucfirst($key) . ' ' .ucfirst($relProperty['fieldName']));
                $condition->setRelation($key);
                $condition->setEntity($relation['targetEntity']);
                $condition->setAttribute($relProperty['fieldName']);
                $condition->setType($relProperty['type']);

                $rules[] = $condition;
            }
            
        }

        return $rules;
    }
}
