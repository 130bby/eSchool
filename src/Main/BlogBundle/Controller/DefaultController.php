<?php

namespace Main\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
		$em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository('MainBlogBundle:Blog')->findAll();
		return $this->render('MainBlogBundle:Default:index.html.twig', array('posts' => $posts));
    }
}
