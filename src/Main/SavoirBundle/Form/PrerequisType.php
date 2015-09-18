<?php

namespace Main\SavoirBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityRepository;

class PrerequisType extends AbstractType
{
	protected $prerequis__objects_array;
	public $mapped_element = false;
	public function __construct($prerequis__objects_array = null)
	{
		$this->prerequis__objects_array = $prerequis__objects_array;
	}
	
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		if (!empty($this->prerequis__objects_array))
		{
			if (!$this->mapped_element)
			{
			$builder
				->add('theme', 'entity', array( 'class' => 'MainThemeBundle:Theme','property' => 'name'))
				->add('savoir', 'entity', array( 'class' => 'MainSavoirBundle:Savoir','property' => 'name'));
				$this->mapped_element = true;
			}
			else
			{
			$current = array_shift($this->prerequis__objects_array);
			$builder
				->add('theme', 'entity', array( 'class' => 'MainThemeBundle:Theme','property' => 'name','attr' => array('readonly' => true),'choices' => $current->theme))
				->add('savoir', 'entity', array( 'class' => 'MainSavoirBundle:Savoir','property' => 'name','attr' => array('readonly' => true),'choices' => $current->savoir));
			}
		}
		else
		{
			$builder
				->add('theme', 'entity', array( 'class' => 'MainThemeBundle:Theme','property' => 'name'))
				->add('savoir', 'entity', array( 'class' => 'MainSavoirBundle:Savoir','property' => 'name'));
		}		
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
		$resolver->setDefaults(array(
        'data_class' => 'Main\SavoirBundle\Form\ModelForPrerequisForm',
    ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'main_savoirbundle_prerequis';
    }
}
