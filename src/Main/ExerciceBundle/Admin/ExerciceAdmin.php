<?php

namespace Main\ExerciceBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ExerciceAdmin extends Admin
{

    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        // use $fileFieldOptions so we can add other options to the field
        $fileFieldOptions = array('required' => false, 'label' => 'Image pour l\'énoncé');
        if ($this->getSubject() && ($webPath = $this->getSubject()->getWebPath())) {
            // get the container so the full path to the image can be set
            $container = $this->getConfigurationPool()->getContainer();
            $fullPath = $container->get('request')->getBasePath().'/'.$webPath;

            // add a 'help' option containing the preview's img tag
            $fileFieldOptions['help'] = '<img src="'.$fullPath.'" class="admin-preview" />';
        }	

        $formMapper
            ->add('titre')
            ->add('texte', null, array('label' => 'Enoncé / Question', 'attr' => array('class' => 'ckeditor')))
            ->add('file', 'file', $fileFieldOptions)
            ->add('niveau', 'choice', array('label' => 'Difficulté', 'choices'   => array(
					'1' => 'facile','2' => 'moyen','3' => 'difficile','4' => 'très difficile')))
            ->add('type', 'choice', array('label' => "Type d'exercice", 'choices'   => array(
					'1' => 'QCM','2' => 'Question simple','3' => 'Texte à trous')))
            ->add('reponse1', 'textarea', array('label' => 'Réponse 1', 'attr' => array('class' => 'ckeditor')))
            ->add('reponse2', 'textarea', array('required' => false, 'label' => 'Réponse 2', 'attr' => array('class' => 'ckeditor')))
            ->add('reponse3', 'textarea', array('required' => false, 'label' => 'Réponse 3', 'attr' => array('class' => 'ckeditor')))
            ->add('reponse4', 'textarea', array('required' => false, 'label' => 'Réponse 4', 'attr' => array('class' => 'ckeditor')))
            ->add('reponseJuste', 'choice', array('label' => 'Réponse(s) Juste(s)', 'choices'   => array(
					'1' => '1','2' => '2','3' => '3','4' => '4'),'multiple'  => true,'expanded'  => true))
            ->add('init', null, array('label' => 'Initialisation des variables aléatoires'))
            ->add('temp', null, array('label' => 'Variables temporaires'))
            ->add('savoir', 'entity', array( 'class' => 'MainSavoirBundle:Savoir','property' => 'name'))
			;
				
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('titre', null, array('label' => 'Titre'))
            ->add('texte', null, array('label' => 'Enoncé'))
            ->add('savoir', null, array('label' => 'Savoir'))
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('titre')
            ->add('niveau', 'choice', array('label' => 'Difficulté', 'choices'   => array(
					'1' => 'facile','2' => 'moyen','3' => 'difficile','4' => 'très difficile')))
            ->add('savoir',null,array('sortable'=>true,
            'sort_field_mapping'=> array('fieldName'=>'name'),
            'sort_parent_association_mappings' => array(array('fieldName'=>'savoir')
            )))
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