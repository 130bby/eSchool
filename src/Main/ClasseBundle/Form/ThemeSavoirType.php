<?php

namespace Main\ClasseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Main\ThemeBundle\Entity\ThemeRepository;

class ThemeSavoirType extends AbstractType
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
		// on récupère la liste des thèmes associées aux matières des classes du user courant (ouf)
		$sc = $this->sc;
		$em = $sc->get('doctrine')->getManager();
		$matieres = array();
		$classes = $em->getRepository('MainClasseBundle:Classe')->findBy(array('owner' => $sc->get('security.context')->getToken()->getUser()));
		foreach ($classes as $classe)
			$matieres[] = $classe->getMatiere()->getId();
		$matieres = array_unique($matieres);	
		
		$builder
            ->add('theme','entity', array('class' => 'MainThemeBundle:Theme','property' => 'name','required' => false,
						'query_builder' => function(ThemeRepository $er ) use ( $matieres ) {
								return $er->createQueryBuilder('t')
										  ->join('t.matiere', 'm')
										  ->where($er->createQueryBuilder('t')->expr()->in('m.id', '?1'))
										  ->setParameter(1, $matieres);}
			))
            ->add('savoir','choice', array('choices' => array(),'required' => false));
    }

	
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'main_classebundle_themesavoir';
    }
}
