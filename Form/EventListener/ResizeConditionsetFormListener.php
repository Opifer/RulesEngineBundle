<?php

namespace Opifer\RulesEngineBundle\Form\EventListener;

use Opifer\RulesEngine\Condition\ConditionSet;
use Opifer\RulesEngine\RulesEngine;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\EventListener\ResizeFormListener;

/**
 * Maps the newly created rulesengine conditions to the form
 */
class ResizeConditionSetFormListener extends ResizeFormListener
{
    /** @var RulesEngine */
    protected $rulesEngine;

    /**
     * Constructor
     *
     * @param RulesEngine $rulesEngine
     * @param array $type
     * @param array $options
     * @param bool $allowAdd
     * @param bool $allowDelete
     * @param bool $deleteEmpty
     */
    public function __construct(RulesEngine $rulesEngine, $type, array $options = array(), $allowAdd = false, $allowDelete = false, $deleteEmpty = false)
    {
        parent::__construct($type, $options, $allowAdd, $allowDelete, $deleteEmpty);

        $this->rulesEngine = $rulesEngine;
    }

    /**
     * {@inheritDoc}
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (null === $data) {
            $data = array();
        }

        try {
            $data = $this->rulesEngine->deserialize($data);
        } catch(\Exception $e) {
            $data = new ConditionSet();
        }

        if (!is_array($data) && !($data instanceof \Traversable && $data instanceof \ArrayAccess)) {
            throw new UnexpectedTypeException($data, 'array or (\Traversable and \ArrayAccess)');
        }

        // First remove all rows
        foreach ($form as $name => $child) {
            $form->remove($name);
        }

        // Then add all rows again in the correct order
        foreach ($data as $name => $value) {
            $form->add($name, $this->type, array_replace(array(
                'property_path' => '['.$name.']',
            ), $this->options));
        }
    }
}
