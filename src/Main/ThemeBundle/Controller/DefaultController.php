<?php

namespace Main\ThemeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('MainThemeBundle:Default:index.html.twig', array('name' => $name));
    }
}
