<?php

namespace Main\UserBundle\Listener;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Custom login listener.
 */
class LoginListener
{
	/** @var \Symfony\Component\Security\Core\SecurityContext */
	private $securityContext;
	
	/** @var \Doctrine\ORM\EntityManager */
	private $em;
	
	/** @var \Doctrine\ORM\EntityManager */
	private $container;

	/**
	 * Constructor
	 * 
	 * @param SecurityContext $securityContext
	 * @param Doctrine        $doctrine
	 */
	public function __construct(SecurityContext $securityContext, Doctrine $doctrine, $container)
	{
		$this->securityContext = $securityContext;
		$this->em              = $doctrine->getEntityManager();
		$this->container       = $container;
	}
	
	/**
	 * Do the magic.
	 * 
	 * @param InteractiveLoginEvent $event
	 */
	public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
	{
		if ($this->securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
			// user has just logged in
		}
		
		if ($this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
			// user has logged in using remember_me cookie
		}
		
		$user = $event->getAuthenticationToken()->getUser();
		$matieres = array();
		
		if ($this->securityContext->isGranted('ROLE_ELEVE'))
		{
			$matieres = $this->em->getRepository('MainMatiereBundle:Matiere')->findAll();
			foreach ($matieres as $key => $matiere)
			{
				$matiere_array['id'] = $matiere->getId();
				$matiere_array['name'] = $matiere->getName();
				$matiere_array['themes'] = array();
				$matieres[$key] = $matiere_array;
			}	

			$themes = $this->em->createQuery("SELECT IDENTITY(t.matiere) as matiere, t.id as id, t.name as name FROM MainThemeBundle:Theme t")->getArrayResult();
			$themes = $this->em->getRepository('MainUserBundle:ThemeUser')->setAvailable($themes,$user);
			foreach ($themes as $theme)
			{
				foreach ($matieres as $key => $matiere)
				{
					$current_matiere = $theme['matiere'];
					if ($current_matiere !== NULL && $current_matiere == $matiere['id'])
						$matieres[$key]['themes'][] = $theme;
				}
			}
		}

		$session = $this->container->get('session');
		$session->set('matieres', $matieres);
		$session->save();
	}
}