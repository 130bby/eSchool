<?php

namespace Main\ClasseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CalendrierType extends AbstractType
{
	private $sc;
	private $classe;
	
	public function __construct($classe,$sc) {
    $this->classe = $classe;
    $this->sc = $sc;
	}
	
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$builder->add('elements', 'collection', array('label' => " ",'type' => new CalendrierElementType($this->classe,$this->sc),'allow_add' => true,'allow_delete' => true,'by_reference' => false));
    }
    

    /**
     * @return string
     */
    public function getName()
    {
        return 'main_classebundle_calendrier';
    }
}
