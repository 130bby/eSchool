<?php

namespace Main\ClasseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ClasseType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pays', 'country', array('label' => 'Pays:' , 'preferred_choices' => array('FR'), 'attr' => array('placeholder' => 'Pays')))
            ->add('region','text',array('label' => 'Region:', 'required' => false))
            ->add('etablissement','text',array('label' => 'Etablissement:', 'required' => false))
            ->add('name','text',array('label' => 'Nom de classe:', 'required' => false))
            ->add('start','date',array( 'widget' => 'single_text', 'html5' => false, 'attr' => ['class' => 'js-datepicker'], 'label' => 'Date de début:'))
            ->add('end','date',array( 'widget' => 'single_text', 'html5' => false, 'attr' => ['class' => 'js-datepicker'], 'label' => 'Date de fin:'))
            ->add('status','checkbox',array('label' => 'Classe active:', 'required' => false))
            ->add('theme',null,array('label' => 'Thème:'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Main\ClasseBundle\Entity\Classe'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'main_classebundle_classe';
    }
}
