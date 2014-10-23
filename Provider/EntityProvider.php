<?php

namespace Opifer\RulesEngineBundle\Provider;

use Doctrine\ORM\EntityManager;

use Opifer\CrudBundle\Doctrine\EntityHelper;
use Opifer\RulesEngine\Rule\Condition\AttributeCondition;
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

            $className = $this->entityHelper->getMetaData($this->context)->getName();
            $className = (false === strpos($className, '\\')) ? $className : substr($className, strrpos($className, '\\') + 1);

            $condition = new AttributeCondition();
            $condition
                ->setName($property['fieldName'])
                ->setEntity($this->context)
                ->setAttribute($property['fieldName'])
                ->setType($property['type'])
            ;

            $rules[] = $condition;
        }

        return $rules;
    }
}
