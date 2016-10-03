<?php

namespace Main\BlogBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class BlogAdmin extends Admin
{

    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        // use $fileFieldOptions so we can add other options to the field
        $formMapper
            ->add('titre')
			->add('description', null, array('required' => false, 'attr' => array('class' => 'ckeditor')))
            ->add('examen', 'entity', array( 'class' => 'MainClasseBundle:Examen','property' => 'name'));
			;
				
    }

    public function prePersist($blog)
    {
        $blog->setDate(new \Datetime());
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('titre', null, array('label' => 'Titre'))
            ->add('description', null, array('label' => 'Description'))
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('titre')
        ;
    }
}