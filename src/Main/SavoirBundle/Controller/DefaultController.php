<?php

namespace Main\SavoirBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function improveAction()
    {
        $em = $this->getDoctrine()->getManager();
		$themes_raw = $em->getRepository('MainUserBundle:ThemeUser')->findby(array('user' => $this->container->get('security.context')->getToken()->getUser()));
		$themes = $em->getRepository('MainSavoirBundle:Savoir')->getSavoirsAAmeliorer($this->container->get('security.context')->getToken()->getUser(), $themes_raw);
		return $this->render('MainSavoirBundle:Default:improve.html.twig', array('themes' => $themes));
    }
	
    public function getArbreAction($theme_id)
    {
		$em = $this->getDoctrine()->getManager();

		$arbre = $em->getRepository('MainSavoirBundle:Savoir')->getArbre($theme_id, $this->container->get('security.context')->getToken()->getUser());
		if(isset($arbre['evaluations']))
			$evaluations = $arbre['evaluations'];
		else
			$evaluations = array();
		unset($arbre['evaluations']);

		$epreuves = $em->getRepository('MainUserBundle:SavoirUser')->findByUser($this->container->get('security.context')->getToken()->getUser());
		if ($epreuves == NULL)
			$evaluation = true;
		else
			$evaluation = false;
		return $this->render('MainSavoirBundle:Default:arbre.html.twig', array('arbre' => $arbre, 'evaluation' => false, 'evaluations' => $evaluations));
    }
	
}
