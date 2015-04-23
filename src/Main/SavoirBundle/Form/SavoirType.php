<?php

namespace Main\SavoirBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SavoirType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array('label' => 'Nom'))
            ->add('definition', null, array('label' => 'Définition'))
            ->add('objectifs', null, array('label' => 'Objectif / Utilité'))
            ->add('exemples', null, array('label' => "Exemples d'utilisation"))
            ->add('historique', null, array('label' => 'Rappel historique'))
            ->add('score_mini', null, array('label' => 'Score minimum requis (en %)'))
            ->add('prerequis', 'entity', array( 'class' => 'MainSavoirBundle:Savoir',
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
            'data_class' => 'Main\SavoirBundle\Entity\Savoir'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'main_savoirbundle_savoir';
    }
}
