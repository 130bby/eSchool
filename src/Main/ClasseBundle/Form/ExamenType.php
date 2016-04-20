<?php

namespace Main\ClasseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Main\ClasseBundle\Entity\ClasseRepository;

class ExamenType extends AbstractType
{
	private $sc;
	
	public function __construct($sc) {
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
            ->add('name')
            ->add('debut')
            ->add('fin')
            ->add('classe','entity', array('class' => 'MainClasseBundle:Classe','property' => 'name',
						'query_builder' => function(ClasseRepository $er ) use ( $sc ) {
								return $er->createQueryBuilder('c')
										  ->where('c.owner = ?1')
						->setParameter(1, $sc->get('security.context')->getToken()->getUser());}
			))
            ->add('savoirs', 'collection', array('type' => new ThemeSavoirType($sc),'allow_add'    => true,'allow_delete' => true));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Main\ClasseBundle\Entity\Examen'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'main_classebundle_examen';
    }
	
	
}
