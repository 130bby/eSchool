<?php

namespace Main\MatiereBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('MainMatiereBundle:Default:index.html.twig', array('name' => $name));
    }
}
