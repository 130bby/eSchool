<?php

namespace Main\CoursBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function showAction($savoir_id)
    {
		$em = $this->getDoctrine()->getManager();
        $cours = $em->getRepository('MainCoursBundle:Cours')->findBy(array('savoir' => $savoir_id));
        $savoir = $em->getRepository('MainSavoirBundle:Savoir')->find($savoir_id);
		$prerequis = array();
		$enfants = array();
		foreach ($savoir->getPrerequis() as $prerekiki)
			$prerequis[] = $em->getRepository('MainSavoirBundle:Savoir')->find($prerekiki);
			
        $savoirs = $em->getRepository('MainSavoirBundle:Savoir')->findAll();
		foreach ($savoirs as $savoir_aimer)
		{
			if (in_array($savoir->getId(),$savoir_aimer->getPrerequis()))
				$enfants[] = $savoir_aimer;
		}
		return $this->render('MainCoursBundle:Default:show.html.twig', array('cours' => $cours,'prerequis' => $prerequis,'enfants' => $enfants));
    }
}
