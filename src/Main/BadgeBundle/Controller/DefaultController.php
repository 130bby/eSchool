<?php

namespace Main\BadgeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
		$badges = $em->getRepository('MainUserBundle:BadgeUser')->getBadges($this->container->get('security.context')->getToken()->getUser());
		foreach ($badges as $key => $badge)
		{
			if ($badge->getBadge()->getId() == 1)
			{
				foreach ($badge->getThemes() as $key2 => $theme)
				{
					$theme_obj = $em->getRepository('MainThemeBundle:Theme')->find($theme);
					$badge->setTheme($key2,$theme_obj->getName());
				}
			}
		}
		
		return $this->render('MainBadgeBundle:Default:index.html.twig', array('badges' => $badges));
    }
}
