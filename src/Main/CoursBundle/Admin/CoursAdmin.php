<?php

namespace Main\CoursBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class CoursAdmin extends Admin
{

    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        // use $fileFieldOptions so we can add other options to the field
        $formMapper
            ->add('titre')
            ->add('savoir', 'entity', array( 'class' => 'MainSavoirBundle:Savoir','property' => 'name'))
			->add('texte', null, array('required' => false, 'attr' => array('class' => 'ckeditor')))			
			;
				
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('titre', null, array('label' => 'Titre'))
            ->add('texte', null, array('label' => 'Texte'))
            ->add('savoir', null, array('label' => 'Savoir'))
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('titre')
            ->add('savoir',null,array('sortable'=>true,
            'sort_field_mapping'=> array('fieldName'=>'name'),
            'sort_parent_association_mappings' => array(array('fieldName'=>'savoir')
            )))
        ;
    }
}