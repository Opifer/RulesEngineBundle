<?php

namespace Opifer\RulesEngineBundle\Form\Type;

use Opifer\RulesEngine\Operator\Doctrine\Equals;
use Opifer\RulesEngine\Operator\Doctrine\In;
use Opifer\RulesEngine\Operator\OperatorInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OperatorType extends AbstractType
{
    /**
     * @var array
     */
    protected $choices = [];

    /**
     * Constructor
     *
     * @param array $choices
     */
    public function __construct(array $choices)
    {
        $this->choices = $choices;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'expanded' => false,
            'multiple' => false,
            'choice_list' => new ArrayChoiceList($this->choices, function($choice) {
                if ($choice instanceof OperatorInterface) {
                    return get_class($choice);
                }

                return null;
            }),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritDoc}
     *
     * @deprecated
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix()
    {
        return 'operator';
    }
}
