<?php

namespace Main\ClasseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Main\SavoirBundle\Entity\SavoirRepository;

class CalendrierElementType extends AbstractType
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
        $sc = $this->sc;
		$builder
            ->add('savoir','entity',array('class' => 'MainSavoirBundle:Savoir','property' => 'name',"required" => false,
						'query_builder' => function(SavoirRepository $er ) use ( $sc ) {
								return $er->createQueryBuilder('c')
										  ->where('c.theme = ?1')
						->setParameter(1, $this->classe->getTheme());}
			))
            ->add('start')
            ->add('end')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Main\ClasseBundle\Entity\Calendrier'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'main_classebundle_calendrier_element';
    }
}
