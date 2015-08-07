<?php

namespace Opifer\RulesEngineBundle\Form\Type;

use Opifer\RulesEngine\RulesEngine;
use Opifer\RulesEngineBundle\Form\DataTransformer\SerializedConditionSetTransformer;
use Opifer\RulesEngineBundle\Form\EventListener\ResizeConditionSetFormListener;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RulesEngineType extends CollectionType
{
    /** @var RulesEngine */
    protected $rulesEngine;

    /**
     * Constructor
     *
     * @param RulesEngine $rulesEngine
     */
    public function __construct(RulesEngine $rulesEngine)
    {
        $this->rulesEngine = $rulesEngine;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['provider']) {
            $options['options']['provider'] = $options['provider'];
        }

        if ($options['allow_add'] && $options['prototype']) {
            $prototype = $builder->create($options['prototype_name'], $options['type'], array_replace([
                'label' => $options['prototype_name'].'label__',
            ], $options['options']));

            $builder->setAttribute('prototype', $prototype->getForm());
        }

        $transformer = new SerializedConditionSetTransformer($this->rulesEngine);
        $builder->addModelTransformer($transformer);

        $resizeListener = new ResizeConditionSetFormListener(
            $this->rulesEngine,
            $options['type'],
            $options['options'],
            $options['allow_add'],
            $options['allow_delete'],
            $options['delete_empty']
        );

        $builder->addEventSubscriber($resizeListener);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired([
            'provider'
        ]);

        $resolver->setDefaults([
            'allow_add' => true,
            'allow_delete' => true,
            'condition_options' => [],
            'data_class' => 'Opifer\RulesEngine\Condition\ConditionSet',
            'type' => 'condition'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'rulesengine';
    }
}
