<?php

namespace Main\UserBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class UserAdmin extends Admin
{

	public function createQuery($context = 'list')
	{
		$query = parent::createQuery($context);
		$query->andWhere($query->expr()->like($query->getRootAlias() . '.roles', ':my_param'));
		$query->setParameter('my_param', '%ROLE_ELEVE%');
		return $query;
	}

    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('first_name', 'text', array('label' => 'Prénom'))
            ->add('last_name', 'text', array('label' => 'Nom'))
            ->add('birthday', 'birthday', array('label' => 'Date de naissance'))
            ->add('country', 'country', array('label' => 'Pays'))
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('lastName', null, array('label' => 'Prénom'))
            ->add('firstName', null, array('label' => 'Nom'))
            ->add('country', null, array('label' => 'Pays'))
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('first_name', null, array('label' => 'Prénom'))
            ->addIdentifier('last_name', null, array('label' => 'Nom'))
            ->add('birthday', null, array('label' => 'Date de naissance'))
            ->add('country', 'country', array('label' => 'Pays'))
        ;
    }
}