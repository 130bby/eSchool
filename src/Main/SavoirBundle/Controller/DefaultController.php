<?php

namespace Main\SavoirBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function improveAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		$theme_filter = $this->get('request')->request->get('theme_filter');
		// var_dump($request);
		$perfectionnement = $this->get('request')->request->get('perfectionnement');
		$a_decouvrir = $this->get('request')->request->get('a_decouvrir');
		if ($theme_filter != null)
		{
			$themes_raw = $em->getRepository('MainUserBundle:ThemeUser')->findby(array('user' => $this->container->get('security.context')->getToken()->getUser(),'theme' => $theme_filter));
		}
		else
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

	public function ajaxAdminPrerequisAction()
	{
		$em = $this->getDoctrine()->getManager();
		$savoirs_array = array();
		$theme = $em->getRepository('MainThemeBundle:Theme')->find($this->container->get('request')->request->get('id'));
		$savoirs = $em->getRepository('MainSavoirBundle:Savoir')->findByTheme($theme);
		foreach ($savoirs as $savoir)
		{
			$savoirs_array[] = $savoir->getId();
		}
		return new Response(json_encode($savoirs_array));
	}
	
}
