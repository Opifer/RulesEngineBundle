<?php

namespace Opifer\RulesEngineBundle\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Opifer\RulesEngineBundle\Provider\Pool;
use Opifer\RulesEngineBundle\Provider\ProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Opifer\RulesEngine\Operator\Doctrine\Equals;
use Opifer\RulesEngine\Operator\Doctrine\In;

class ConditionType extends AbstractType
{
    /**
     * @var Pool
     */
    protected $providers;

    /**
     * Constructor
     *
     * @param Pool $providers
     */
    public function __construct(Pool $providers)
    {
        $this->providers = $providers;
    }

    /**
     * Defines the left, operator and right formfields for the condition formtype.
     * The left & right can either be a selectbox or a textfield if no selectoptions are provided.
     *
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $provider = $this->providers->getProvider($options['provider']);

        if ($this->getLefts($provider, $options)) {
            $builder->add('left', 'choice', [
                'expanded' => false,
                'multiple' => false,
                'choices' => $this->getLefts($provider, $options),
                'attr' => [
                    'class' => 'left',
                    'data-provider' => $options['provider']
                ]
            ]);
        } else {
            $builder->add('left', 'text', [
                'attr' => [
                    'class' => 'left'
                ]
            ]);
        }

        $builder->add('operator', new OperatorType($provider->getOperators()), [
            'attr' => [
                'class' => 'operator'
            ]
        ]);

        // Since we use javascript to change the `right` options, we need to make sure the right options match
        // the selected left option at time of submitting the form.
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($provider, $options) {
            $form = $event->getForm();
            $submittedData = $event->getData();

            $form->add('right', 'choice', [
                'expanded' => false,
                'multiple' => false,
                'choices' => $provider->getRightsForLeft($submittedData['left']),
                'attr' => [
                    'class' => 'right'
                ]
            ]);
        });

        // Set the right choices on PRE_SET_DATA,
        // so we can retrieve the rights related to the selected left value.
        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) use ($provider, $options) {
            $condition = $event->getData();
            $form = $event->getForm();

            if ($provider->getRights()) {
                if ($condition) {
                    $left = $condition->getLeft();
                } else {
                    $lefts = $this->getLefts($provider, $options);
                    reset($lefts);
                    $left = key($lefts);
                }

                $form->add('right', 'choice', [
                    'expanded' => false,
                    'multiple' => false,
                    'choices' => $provider->getRightsForLeft($left),
                    'attr' => [
                        'class' => 'right'
                    ]
                ]);
            } else {
                $form->add('right', 'text', [
                    'attr' => [
                        'class' => 'right'
                    ]
                ]);
            }
        });
    }

    /**
     * Get Left options
     *
     * Get any preset left options if defined on either the providers' getLefts method or the form-type options.
     * The providers' getLefts method has priority over the 'lefts' formtype option.
     *
     * @param ProviderInterface $provider
     * @param array $options
     *
     * @return array|bool
     */
    protected function getLefts(ProviderInterface $provider, array $options = [])
    {
        if ($provider->getLefts()) {
            return $provider->getLefts();
        } elseif ($options['lefts']) {
            $lefts = [];
            foreach ($options['lefts'] as $key => $left) {
                if (is_object($left)) {
                    $lefts[$left->getId()] = $left->getDisplayName();
                } else {
                    $lefts[$key] = $left;
                }
            }

            return $lefts;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            'provider'
        ]);

        $resolver->setDefaults([
            'data_class' => 'Opifer\RulesEngine\Condition\Condition',
            'provider' => '',
            'lefts' => null
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'condition';
    }
}
