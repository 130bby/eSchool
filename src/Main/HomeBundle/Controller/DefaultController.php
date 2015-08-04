<?php

namespace Main\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class DefaultController extends Controller
{
    public function indexAction($data = false)
    {

        // get the error if bad login credentials
		$request = $this->getRequest();
		$session = $request->getSession();
        if ($request->attributes->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContextInterface::AUTHENTICATION_ERROR);
        } elseif (null !== $session && $session->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContextInterface::AUTHENTICATION_ERROR);
            $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);
        } else {
            $error = null;
        }
		
		$em = $this->getDoctrine()->getManager();
		$matieres = array();
		if ($this->container->get('security.context')->isGranted('ROLE_ELEVE'))
		{
			$matieres = $em->getRepository('MainMatiereBundle:Matiere')->findAll();
			foreach ($matieres as $key => $matiere)
			{
				$matiere_array['id'] = $matiere->getId();
				$matiere_array['name'] = $matiere->getName();
				$matiere_array['themes'] = array();
				$matieres[$key] = $matiere_array;
			}	

			$themes = $em->createQuery("SELECT IDENTITY(t.matiere) as matiere, t.id as id, t.name as name FROM MainThemeBundle:Theme t")->getArrayResult();
			$themes = $em->getRepository('MainUserBundle:ThemeUser')->setAvailable($themes,$this->container->get('security.context')->getToken()->getUser());
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

        return $this->render('MainHomeBundle:Default:index.html.twig', array('matieres' => $matieres,'error' => $error));
    }
	
}
