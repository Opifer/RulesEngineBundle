<?php

namespace Opifer\RulesEngineBundle\Provider;

use Doctrine\ORM\EntityManager;
use Opifer\CrudBundle\Doctrine\EntityHelper;
use Opifer\RulesEngine\Environment\DoctrineEnvironment;
use Opifer\RulesEngine\Rule\Condition\AttributeCondition;
use Opifer\RulesEngine\Rule\Condition\RelationCondition;
use Opifer\RulesEngine\Rule\Condition\Condition;
use Opifer\RulesEngine\Rule\Rule;
use Opifer\RulesEngine\Rule\RuleSet;

use Doctrine\Common\Inflector\Inflector;
use Symfony\Component\HttpFoundation\RequestStack;

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
     * @var array 
     */
    protected $paramDefaults = [
        'offset' => 0,
        'limit' => 0,
        'orderby' => ['title', 'created_at'],
        'order' => ['ASC','DESC']
    ];
    
    /**
     * @var RequestStack 
     */
    protected $request;

    /**
     * Constructor
     *
     * @param EntityHelper  $entityHelper
     * @param EntityManager $em
     */
    public function __construct(EntityHelper $entityHelper, EntityManager $em, RequestStack $requestStack)
    {
        $this->request =  $requestStack->getCurrentRequest();
        $this->entityHelper = $entityHelper;
        $this->em = $em;
    }
    
    /**
     * {@inheritDoc}
     */
    public function buildRules()
    {
        $rules = [];

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
//        dump(get_class($rule));
        $environment = new DoctrineEnvironment();

        // use exotic alias because we use entity's own repository
        $this->qb = $this->em->getRepository($this->getEntity($rule))->createQueryBuilder('a');

        $environment->queryBuilder = $this->qb;

        return $environment->evaluate($rule);
    }

    /**
     * Get the entity from the passed rule
     *
     * @param Rule $rule
     */
    protected function getEntity(Rule $rule)
    {
        if ($rule instanceof RuleSet) {
            foreach ($rule->getChildren() as $child) {
                return $child->getEntity();
            }

            throw new \Exception(sprintf('The rule %s and non of its children have an entity', get_class($rule)));
        }

        return $rule->getEntity();
    }
    
    /**
     * Update query builder to include additional parameters
     * 
     * @param integer $id
     * @param array $params
     */
    public function setQueryParams($id, $params = [])
    {
        $requestQuery = $this->request->query->get('query_id');
        
        if(isset($requestQuery[$id])) {
            $paramsQuery = $requestQuery[$id];
        } elseif(isset($params['query'])) {
            $paramsQuery = $params['query'];
        } else {
            $paramsQuery = [];
        }
        
        $query = array_merge([
            'limit' => null,
            'offset' => null,
            'orderby' => null,
            'order' => null
        ], $paramsQuery);
        
        if($query['offset'] !== null && $query['offset'] >= $this->paramDefaults['offset']) {
            $this->qb->setFirstResult($query['offset']);
        }
        
        if($query['limit'] !== null && $query['limit'] >= $this->paramDefaults['limit']) {
            $this->qb->setMaxResults($query['limit']);
        }
        
        if(in_array($query['orderby'], $this->paramDefaults['orderby'])) {
            $order = in_array(strtoupper($query['order']), $this->paramDefaults['order']) ? $query['order'] : $this->paramDefaults['order'][0];

            $this->qb->orderBy('a.'.Inflector::camelize($query['orderby']), strtoupper($order));
        }
    }
}
