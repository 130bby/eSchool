<?php

namespace Main\SavoirBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Doctrine\ORM\EntityRepository;
use Main\SavoirBundle\Form\PrerequisType;
use Main\SavoirBundle\Form\ModelForPrerequisForm;

class SavoirAdmin extends Admin
{

	public function getFormTheme()
	{
		return array_merge(
			parent::getFormTheme(),
			array('MainSavoirBundle:Admin:admin.theme.html.twig')
		);
	}
	
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        // use $fileFieldOptions so we can add other options to the field
        $fileFieldOptions = array('required' => false, 'label' => 'Icone');
        if ($this->getSubject() && ($webPath = $this->getSubject()->getWebPath())) {
            // get the container so the full path to the image can be set
            $container = $this->getConfigurationPool()->getContainer();
            $fullPath = $container->get('request')->getBasePath().'/'.$webPath;

            // add a 'help' option containing the preview's img tag
            $fileFieldOptions['help'] = '<img src="'.$fullPath.'" class="admin-preview" />';
        }	
	
        $formMapper
            ->add('name', null, array('label' => 'Nom'))
            ->add('definition', null, array('label' => 'Définition'))
            ->add('objectifs', null, array('label' => 'Objectif / Utilité'))
            ->add('exemples', null, array('label' => "Exemples d'utilisation"))
            ->add('historique', null, array('label' => 'Rappel historique'))
            ->add('classe', 'choice', array('label' => 'Classe', 'choices'   => array(
					'6' => '6ème','5' => '5ème','4' => '4ème','3' => '3ème')))
            ->add('score_mini', null, array('label' => 'Score minimum requis (en %)'))
            ->add('file', 'file', $fileFieldOptions)
            ->add('theme', 'entity', array( 'class' => 'MainThemeBundle:Theme','property' => 'name'));

		$prerequis__objects_array = array();
		foreach ($this->getSubject()->getPrerequis() as $prerequis_id)
		{
			$em = $this->getConfigurationPool()->getContainer()->get('doctrine')->getEntityManager();
			$savoir = $em->getRepository('MainSavoirBundle:Savoir')->findById($prerequis_id);
			$theme = $em->getRepository('MainThemeBundle:Theme')->findById($savoir[0]->getTheme()->getId());
			$prerequis = new ModelForPrerequisForm();
			$prerequis->savoir = $savoir;
			$prerequis->theme = $theme;
			$prerequis__objects_array[] = $prerequis;
		}

		$formMapper->add('prerequis','collection', array(
				'type'               => new PrerequisType($prerequis__objects_array),
				'allow_add'          => true,
				'allow_delete'       => true,
				'cascade_validation' => false,
				'by_reference'       => false,
				'delete_empty'       => true,
				'options'            => array( 'label' => false ),
		));
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
            ->add('matiere', null, array('label' => 'Matière'))
        ;
    }
	
    public function prePersist($image) {
        $this->manageFileUpload($image);
    }

    public function preUpdate($image) {
        $this->manageFileUpload($image);
    }

    private function manageFileUpload($image) {
        if ($image->getFile()) {
            $image->refreshUpdated();
        }
    }	
}