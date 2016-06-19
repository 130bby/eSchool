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
            ->add('pays', 'country', array('label' => 'Pays' , 'preferred_choices' => array('FR'), 'attr' => array('placeholder' => 'Pays')))
            ->add('region')
            ->add('etablissement')
            ->add('name','text',array('label' => 'Nom de classe', 'required' => false))
            ->add('start')
            ->add('end')
            ->add('status','checkbox',array('label' => 'Actif', 'required' => false))
            ->add('theme')
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
