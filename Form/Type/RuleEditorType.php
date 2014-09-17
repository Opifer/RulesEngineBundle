<?php

namespace Opifer\RulesEngineBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Description of PresentationEditorType
 *
 * @author dylan
 */
class RuleEditorType extends AbstractType
{

    /**
     * {@inheritDoc}
     */
    public function getDefaultOptions(array $options)
    {
        $options = parent::getDefaultOptions($options);
        $options['provider'] = 'doctrine';
        $options['context'] = 'null';

        return $options;
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'compound' => false,
            'provider' => 'doctrine',
            'context' => null,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['provider'] = $options['provider'];
        $view->vars['context'] = $options['context'];

        parent::buildView($view, $form, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setAttribute('provider', $options['provider'])
            ->setAttribute('context', $options['context'])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'ruleeditor';
    }
}
