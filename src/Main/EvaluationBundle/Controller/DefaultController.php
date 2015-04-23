<?php

namespace Main\EvaluationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Main\UserBundle\Entity\SavoirUser as SavoirUser;
use Main\UserBundle\Entity\EvaluationUser as EvaluationUser;

class DefaultController extends Controller
{
    public function adminRefreshAjaxAction()
    {
		$em = $this->getDoctrine()->getManager();
		$savoirs = $em->getRepository('MainSavoirBundle:Savoir')->findby(array('theme' => $this->container->get('request')->request->get('theme_id')));
		$html = "";
		$uniqid = $this->container->get('request')->request->get('uniqid');
		$evaluation = $em->getRepository('MainEvaluationBundle:Evaluation')->find($this->container->get('request')->request->get('eval_id'));
		foreach($savoirs as $savoir)
		{
			if($evaluation != NULL && in_array($savoir->getId(),$evaluation->getSavoirs()))
				$checked = 'checked="checked"';
			else 
				$checked = "";
			$html.= '<li><label><input type="checkbox" id="'.$uniqid.'"_savoirs_'.$savoir->getId().'" name="'.$uniqid.'[savoirs][]" '.$checked.' value="'.$savoir->getId().'" /> 								
			'.$savoir->getName().'</label></li>';
		}
		return new Response($html,200);
    }
	
    public function passAction($evaluation_id)
    {
        $em = $this->getDoctrine()->getManager();
        $evaluation = $em->getRepository('MainEvaluationBundle:Evaluation')->find($evaluation_id);
		$exercices = array();
		foreach ($evaluation->getSavoirs() as $savoir_id)
		{
			$savoir = $em->getRepository('MainSavoirBundle:Savoir')->find($savoir_id);
			$exercices = array_merge($exercices, $em->getRepository('MainExerciceBundle:Exercice')->getExercicesEpreuve($savoir,false));
		}
		
        return $this->render('MainEvaluationBundle:Default:pass.html.twig', array('exercices' => $exercices, 'evaluation' => $evaluation));
    }
	
    public function passedAction($evaluation_id)
    {
		$request = $this->getRequest()->request;
        $em = $this->getDoctrine()->getManager();
        $evaluation = $em->getRepository('MainEvaluationBundle:Evaluation')->find($evaluation_id);
		
		//gestion du temps
		$temps_limite = new \DateTime('00:15:00');
		$temps_intervalle = $temps_limite->diff(new \Datetime('00:'.$request->get('temps')));
		$temps = new \DateTime('00:00:00');
		$temps->sub($temps_intervalle);
		
		$score = (int)$request->get('score')*100/(count($evaluation->getSavoirs())*6);
		$savoirs = array();

		if ($score > 70)
		{
			$evaluation_user = new EvaluationUser();
			$evaluation_user->setUser($this->container->get('security.context')->getToken()->getUser());
			$evaluation_user->setEvaluation($evaluation);
			$evaluation_user->setScore($score);
			$evaluation_user->setTemps($temps);
			$evaluation_user->setDate(new \Datetime());
			$em->persist($evaluation_user);
			
			foreach ($evaluation->getSavoirs() as $savoir_id)
			{
				$savoir = $em->getRepository('MainSavoirBundle:Savoir')->find($savoir_id);
				$savoirs[] = $savoir;
				$savoir_user = new SavoirUser();
				$savoir_user->setUser($this->container->get('security.context')->getToken()->getUser());
				$savoir_user->setSavoir($savoir);
				$savoir_user->setScore($savoir->getScoreMini());
				$savoir_user->setTemps($temps);
				$savoir_user->setDate(new \Datetime());
				$em->persist($savoir_user);
			}
	
		}

		$em->flush();
		if ($score > 70)
			$success = true;
		else
			$success = false;

		$badges = $em->getRepository('MainUserBundle:BadgeUser')->setBadges($this->container->get('security.context')->getToken()->getUser(),$score,$evaluation->getTheme(),false,true);
        return $this->render('MainEvaluationBundle:Default:passed.html.twig', array('success' => $success, 'savoirs' => $savoirs, 'badges' => $badges));
    }
	
	
}
