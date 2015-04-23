<?php

namespace Main\ExerciceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ExerciceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre')
            ->add('texte', null, array('label' => 'Enoncé / Question'))
            ->add('niveau', 'choice', array('label' => 'Difficulté', 'choices'   => array(
					'1' => 'facile','2' => 'moyen','3' => 'difficile','4' => 'très difficile')))
            ->add('type', 'choice', array('label' => "Type d'exercice", 'choices'   => array(
					'1' => 'QCM','2' => 'Question simple','3' => 'Texte à trous')))
            ->add('reponse1', null, array('label' => 'Réponse 1'))
            ->add('reponse2', null, array('required' => false, 'label' => 'Réponse 2'))
            ->add('reponse3', null, array('required' => false, 'label' => 'Réponse 3'))
            ->add('reponse4', null, array('required' => false, 'label' => 'Réponse 4'))
            ->add('reponseJuste', 'choice', array('label' => 'Réponse(s) Juste(s)', 'choices'   => array(
					'1' => '1','2' => '2','3' => '3','4' => '4'),'multiple'  => true,'expanded'  => true))
            ->add('savoir', 'entity', array( 'class' => 'MainSavoirBundle:Savoir','property' => 'name'))
			;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Main\ExerciceBundle\Entity\Exercice'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'main_exercicebundle_exercice';
    }
}
