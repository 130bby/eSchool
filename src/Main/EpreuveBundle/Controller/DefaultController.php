<?php

namespace Main\EpreuveBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Main\UserBundle\Entity\SavoirUser as SavoirUser;
use Main\ExerciceBundle\Entity\ExerciceReport as ExerciceReport;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function passAction($savoir_id)
    {
        $em = $this->getDoctrine()->getManager();
        $savoir = $em->getRepository('MainSavoirBundle:Savoir')->find($savoir_id);
        $exercices = $em->getRepository('MainExerciceBundle:Exercice')->getExercicesEpreuve($savoir);
        return $this->render('MainEpreuveBundle:Default:pass.html.twig', array('exercices' => $exercices, 'savoir' => $savoir));
    }

    public function passAjaxAction()
    {
		if($this->container->get('request')->isXmlHttpRequest())
		{
			$em = $this->getDoctrine()->getManager();
			$exercice = $em->getRepository('MainExerciceBundle:Exercice')->find($this->container->get('request')->request->get('id'));
			$reponses = array();
			$reponse1 = $this->container->get('request')->request->get('reponse1');
			$reponse2 = $this->container->get('request')->request->get('reponse2');
			$reponse3 = $this->container->get('request')->request->get('reponse3');
			$reponse4 = $this->container->get('request')->request->get('reponse4');
			$type_exo = $this->container->get('request')->request->get('type_exo');
			$reponse_juste = $this->container->get('request')->request->get('reponse_juste');
			$enonce = $this->container->get('request')->request->get('enonce');

			//cas particulier des exercices a variables aléatoires, type réponse simple
			if ($reponse1 !== NULL)	$reponses[] = $reponse1;
			if ($reponse2 !== NULL)	$reponses[] = $reponse2;
			if ($reponse3 !== NULL)	$reponses[] = $reponse3;
			if ($reponse4 !== NULL)	$reponses[] = $reponse4;
			$success = $em->getRepository('MainExerciceBundle:Exercice')->getReponseExercice($exercice,$type_exo,$reponses,$reponse_juste,$enonce);
		}

		$em->getRepository('MainExerciceBundle:ExerciceStats')->updateStats($exercice, $success);
		$user = $this->container->get('security.context')->getToken()->getUser();
		if (is_object($user))
			$user->setXp($user->getXp()+1);
		$em->flush();
		if ($success === true)
			return new Response('ok');
		else
			return new Response(json_encode(array('ko',$success[1])));
    }
	
    public function passedAction($savoir_id)
    {
		$request = $this->getRequest()->request;
        $em = $this->getDoctrine()->getManager();
        $savoir = $em->getRepository('MainSavoirBundle:Savoir')->find($savoir_id);

		//gestion du temps
		$temps_limite = new \DateTime('00:15:00');
		$temps_intervalle = $temps_limite->diff(new \Datetime('00:'.$request->get('temps')));
		$temps = new \DateTime('00:00:00');
		$temps->sub($temps_intervalle);
		
		if ($this->container->get('security.context')->isGranted('ROLE_ELEVE'))
		{
			$savoir_user = new SavoirUser();
			$savoir_user->setUser($this->container->get('security.context')->getToken()->getUser());
			$savoir_user->setSavoir($savoir);
			$savoir_user->setScore(((int)$request->get('score')/20)*100);
			$user = $this->container->get('security.context')->getToken()->getUser();
			if (((int)$request->get('score')/20)*100 >= $savoir->getScoreMini())
			{
				$savoir_user->setSuccess(true);
				$user->setXp($user->getXp()+10);
			}
			else
			{
				$savoir_user->setSuccess(false);
				$user->setXp($user->getXp()+5);
			}
			$savoir_user->setTemps($temps);
			$savoir_user->setDate(new \Datetime());
			$em->persist($savoir_user);
			$em->flush();
			if (((int)$request->get('score')/20)*100 >= $savoir->getScoreMini())
				$success = true;
			else
				$success = false;
			
			$badges = $em->getRepository('MainUserBundle:BadgeUser')->setBadges($this->container->get('security.context')->getToken()->getUser(),((int)$request->get('score')/20)*100,$savoir->getTheme(),$savoir);
		}
		else
		{
			$savoir_user = array();
			$savoir_user['savoir_id'] = $savoir_id;
			$savoir_user['score'] = $request->get('score')*100/20;
			if (((int)$request->get('score')/20)*100 >= $savoir->getScoreMini())
				$savoir_user['success'] = true;
			else
				$savoir_user['success'] = false;
			$savoir_user['temps'] = $temps;
			
			$session = $this->container->get('session');
			$session->set('savoir_user', $savoir_user);
			$session->save();
			
			$success = $savoir_user['success'];
			$badges = null;
		}
        return $this->render('MainEpreuveBundle:Default:passed.html.twig', array('success' => $success, 'savoir' => $savoir, 'badges' => $badges));
    }
	
    public function reportAction()
    {
 		if($this->container->get('request')->isXmlHttpRequest())
		{
			$em = $this->getDoctrine()->getManager();
			$request = $this->getRequest()->request;
			$exercice = $em->getRepository('MainExerciceBundle:Exercice')->find($request->get('id'));
			$exercice_report = new ExerciceReport();
			$exercice_report->setExercice($exercice);
			$exercice_report->setComment($request->get('comment'));
			$em->persist($exercice_report);
			$em->flush();
			return new Response('ok');
		}
    }
	
}
