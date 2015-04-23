<?php

namespace Main\EvaluationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EvaluationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array('label' => 'Nom'))
            ->add('savoirs', 'entity', array( 'class' => 'MainSavoirBundle:Savoir',
				'property' => 'name', 'multiple' => true, 'expanded' => true))
            ->add('theme', 'entity', array( 'class' => 'MainThemeBundle:Theme','property' => 'name'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Main\EvaluationBundle\Entity\Evaluation'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'main_evaluationbundle_evaluation';
    }
}
