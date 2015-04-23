<?php

namespace Main\EvaluationBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Doctrine\ORM\EntityRepository;

class EvaluationAdmin extends Admin
{

	public function getFormTheme()
	{
		return array_merge(
			parent::getFormTheme(),
			array('MainEvaluationBundle:Admin:admin.theme.html.twig')
		);
	}
	
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', null, array('label' => 'Nom'))
            ->add('niveau', 'choice', array('label' => 'Difficulté', 'choices'   => array(
					'1' => 'facile','2' => 'moyen','3' => 'difficile')))
            ->add('classe', 'choice', array('label' => 'Classe', 'choices'   => array(
					'6' => '6ème','5' => '5ème','4' => '4ème','3' => '3ème')))
            ->add('theme', 'entity', array( 'class' => 'MainThemeBundle:Theme','property' => 'name'));

		if ($this->getSubject()->getId() > 0) {
			$formMapper->add('savoirs', 'entity', array( 'class' => 'MainSavoirBundle:Savoir',
				'query_builder' => function(EntityRepository $er) {
					return $er->createQueryBuilder('s')
					->where('s.id != :id')
					->setParameter('id', $this->getSubject()->getId());
				},
				'property' => 'name', 'multiple' => true, 'expanded' => true));
		}
		else {
			$formMapper->add('savoirs', 'entity', array( 'class' => 'MainSavoirBundle:Savoir',
				'property' => 'name', 'multiple' => true, 'expanded' => true));
		}
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
		$themes = $this->modelManager->createQuery( 'Main\ThemeBundle\Entity\Theme', 't' )
            ->orderBy( 't.name', 'ASC' )
            ->getQuery()
            ->getResult();

        $choices = array ();
        foreach ( $themes as $theme ) {
            $choices [$theme->getId()] = $theme->getName();
        }
        
		$datagridMapper
			->add('name', null, array('label' => 'Nom'))
			->add('theme.id', null, array(
				'field_type' => 'checkbox',
				'label' => 'Theme'
				),
				'choice', 
				array('choices' => $choices));
			
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name', null, array('label' => 'Nom'))
            ->add('theme', null, array('label' => 'Thème'))
            ->add('theme.matiere', null, array('label' => 'Matière'))
        ;
    }
}