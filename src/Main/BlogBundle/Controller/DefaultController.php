<?php

namespace Main\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
		$em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository('MainBlogBundle:Blog')->findBy(array(), array('date' => 'DESC'));
		return $this->render('MainBlogBundle:Default:index.html.twig', array('posts' => $posts));
    }
	
    public function showAction($id)
    {
		$em = $this->getDoctrine()->getManager();
        $post = $em->getRepository('MainBlogBundle:Blog')->find($id);
		return $this->render('MainBlogBundle:Default:show.html.twig', array('post' => $post));
    }
	
}
