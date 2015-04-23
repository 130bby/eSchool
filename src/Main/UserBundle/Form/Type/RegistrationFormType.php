<?php

namespace Main\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\SecurityContext;

class RegistrationFormType extends AbstractType
{
    protected $request;

    public function setRequest(RequestStack $request_stack)
    {
        $this->request = $request_stack->getCurrentRequest();
    }
	
    private $securityContext;

    public function __construct(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;
    }	

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first_name', null, array('label' => 'Prénom'))
            ->add('last_name', null, array('label' => 'Nom'))
			
            ->add('birthday', 'birthday', array('label' => 'Date de naissance'))
            ->add('country', 'country', array('label' => 'Pays'))
			;
		//	var_dump($options);
		if($this->request->attributes->get('_route') == "main_student_register" || ($this->securityContext->isGranted('ROLE_ELEVE')) ) {
		$builder
            ->add('classe', 'choice', array('label' => 'Classe', 'choices'   => array(
					'6' => '6ème','5' => '5ème','4' => '4ème','3' => '3ème')));
		}
			
	}

    public function getParent()
    {
        return 'fos_user_registration';
    }

    public function getName()
    {
        return 'main_user_registration';
    }
}