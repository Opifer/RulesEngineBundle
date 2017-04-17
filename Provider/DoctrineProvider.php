<?php

namespace Opifer\RulesEngineBundle\Provider;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Opifer\RulesEngine\Condition\ConditionSet;
use Opifer\RulesEngine\Context\DoctrineContext;
use Opifer\RulesEngine\Operator\Doctrine\Equals;
use Opifer\RulesEngine\Operator\Doctrine\In;
use Opifer\RulesEngine\RulesEngine;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Doctrine Provider
 */
class DoctrineProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * @var string
     */
    protected $object;

    /**
     * @var ObjectManager
     */
    protected $om;

    /**
     * @var RequestStack
     */
    protected $request;
    
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
     * Constructor
     *
     * @param ObjectManager $om
     * @param RequestStack $requestStack
     */
    public function __construct(ObjectManager $om, RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->om = $om;
    }

    /**
     * {@inheritDoc}
     */
    public function evaluate(ConditionSet $set)
    {
        $qb = $this->om->getRepository($this->getObject())->createQueryBuilder('a');
        $context = new DoctrineContext($qb);

        $rulesEngine = new RulesEngine();
        $rulesEngine->interpret($set, $context);

        return $context->getData();
    }

    /**
     * {@inheritDoc}
     */
    public function getLefts()
    {
        $choices = [];

        foreach($this->getProperties() as $property) {
            $choices[$property['fieldName']] = $property['fieldName'];
        }

        return $choices;
    }

    /**
     * {@inheritDoc}
     */
    public function getOperators()
    {
        return [
            'Equals' => new Equals(),
            'In' => new In()
        ];
    }

    /**
     * get Metadata from entity
     *
     * @return \Doctrine\ORM\Mapping\ClassMetaData
     */
    public function getMetaData()
    {
        return $this->om->getClassMetadata($this->getObject());
    }

    /**
     * Get all the properties for the entity
     *
     * @return array
     */
    public function getProperties()
    {
        return $this->getMetaData()->fieldMappings;
    }

    /**
     * @param string|Object $object
     *
     * @return $this
     */
    public function setObject($object)
    {
        if (is_object($object)) {
            $object = get_class($object);
        }

        $this->object = $object;

        return $this;
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    public function getObject()
    {
        if (is_null($this->object)) {
            throw new \Exception('Trying to retrieve object data for a null object');
        }

        return $this->object;
    }
    
    /**
     * Update query builder to include additional parameters
     * 
     * @param integer $id
     * @param array $params
     */
    //public function setQueryParams($id, $params = [])
    //{
    //    $requestQuery = $this->request->query->get('query_id');
    //
    //    if(isset($requestQuery[$id])) {
    //        $paramsQuery = $requestQuery[$id];
    //    } elseif(isset($params['query'])) {
    //        $paramsQuery = $params['query'];
    //    } else {
    //        $paramsQuery = [];
    //    }
    //
    //    $query = array_merge([
    //        'limit' => null,
    //        'offset' => null,
    //        'orderby' => null,
    //        'order' => null
    //    ], $paramsQuery);
    //
    //    if($query['offset'] !== null && $query['offset'] >= $this->paramDefaults['offset']) {
    //        $this->qb->setFirstResult($query['offset']);
    //    }
    //
    //    if($query['limit'] !== null && $query['limit'] >= $this->paramDefaults['limit']) {
    //        $this->qb->setMaxResults($query['limit']);
    //    }
    //
    //    if(in_array($query['orderby'], $this->paramDefaults['orderby'])) {
    //        $order = in_array(strtoupper($query['order']), $this->paramDefaults['order']) ? $query['order'] : $this->paramDefaults['order'][0];
    //
    //        $this->qb->orderBy('a.'.Inflector::camelize($query['orderby']), strtoupper($order));
    //    }
    //}
}
