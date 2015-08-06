<?php

namespace Opifer\RulesEngineBundle\Form\Type;

use Opifer\RulesEngineBundle\Form\DataTransformer\SerializedConditionSetTransformer;
use Opifer\RulesEngineBundle\Form\EventListener\ResizeRulesengineFormListener;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RulesEngineType extends CollectionType
{
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

        $transformer = new SerializedConditionSetTransformer();
        $builder->addModelTransformer($transformer);

        $resizeListener = new ResizeRulesengineFormListener(
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
