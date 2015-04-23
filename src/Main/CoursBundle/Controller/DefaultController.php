<?php

namespace Main\CoursBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function showAction($savoir_id)
    {
		$em = $this->getDoctrine()->getManager();
        $cours = $em->getRepository('MainCoursBundle:Cours')->findBy(array('savoir' => $savoir_id));
		return $this->render('MainCoursBundle:Default:show.html.twig', array('cours' => $cours));
    }
}
