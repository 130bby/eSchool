<?php

namespace Main\ThemeBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ThemeAdmin extends Admin
{

    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('name', null, array('label' => 'Nom'))
            ->add('matiere', 'entity', array( 'class' => 'MainMatiereBundle:Matiere','property' => 'name'));
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, array('label' => 'Nom'))
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name', null, array('label' => 'Nom'))
            ->add('matiere',null,array('sortable'=>true,
            'sort_field_mapping'=> array('fieldName'=>'name'),
            'sort_parent_association_mappings' => array(array('fieldName'=>'matiere')
            )))
        ;
    }
}