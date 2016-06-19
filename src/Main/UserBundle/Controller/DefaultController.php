<?php

namespace Main\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Main\UserBundle\Entity\ThemeUser as ThemeUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function myHistoryAction()
    {
        $em = $this->getDoctrine()->getManager();
		$epreuves = $em->getRepository('MainUserBundle:SavoirUser')->findByUser($this->container->get('security.context')->getToken()->getUser(),array("date" => "DESC"));

        return $this->render('MainUserBundle:Default:my_history.html.twig', array('epreuves' => $epreuves));
    }

    public function myStatsAction()
    {
        $em = $this->getDoctrine()->getManager();
		//légende pour les semaines précédentes
		$start_week = strtotime("last monday",time());
		$week_number = strftime("%W",$start_week - 7*(24 * 60 * 60 * 7) );
		for ($i=1;$i<8;$i++)
		{
			$weeks[] = strftime("%d %b",$start_week - (7-$i)*(24 * 60 * 60 * 7));
		}
		$legend = json_encode($weeks);
		
		//EPREUVES PASSEES
		$epreuves = $em->createQuery("SELECT COUNT(s.id), WEEK(s.date) as week_number FROM MainUserBundle:SavoirUser s WHERE s.date > '".date('Y-m-d', strtotime('-7 week'))."' AND s.user = ".$this->container->get('security.context')->getToken()->getUser()->getId()." GROUP BY week_number ")->getArrayResult();
		$data_epreuve_passees = array();
		for($i=0;$i<7;$i++)
		{
			foreach ($epreuves as $epreuve)
			{
				if ($epreuve['week_number'] == date("W",strtotime('-'.(8-$i).' week')))
					$data_epreuve_passees[$i] = $epreuve[1];
			}
			if (!isset($data_epreuve_passees[$i]))
				$data_epreuve_passees[$i] = 0;
		}
		//version cumulative
		$cumul = 0;
		foreach ($data_epreuve_passees as $key => $data_epreuve_passee)
		{
			$data_epreuve_passees[$key] += $cumul;
			$cumul += (int)$data_epreuve_passee;
		}
		$data_epreuve_passees = json_encode($data_epreuve_passees);
		
		//SAVOIRS AQUIS
		$savoirs_aquis = $em->createQuery("SELECT IDENTITY(s.savoir) as savoir, WEEK(s.date) as week_number FROM MainUserBundle:SavoirUser s WHERE s.success = 1 AND s.date > '".date('Y-m-d', strtotime('-7 week'))."' AND s.user = ".$this->container->get('security.context')->getToken()->getUser()->getId()." GROUP BY week_number, savoir ")->getArrayResult();
		$data_savoirs_aquis = array();
		foreach ($savoirs_aquis as $savoir)
			$data_by_week[$savoir['week_number']][] = $savoir['savoir'];
		//version cumulative
		// ATTENTION : un savoir aquis 2 fois sur 2 semaines différentes compte double !
		$cumul = 0;
		for($i=0;$i<7;$i++)
		{
			if (isset($data_by_week[date("W",strtotime('-'.(8-$i).' week'))]))
				$cumul += count($data_by_week[date("W",strtotime('-'.(8-$i).' week'))]);
			$data_savoirs_aquis[$i] = $cumul;
		}
		$data_savoirs_aquis = json_encode($data_savoirs_aquis);
		
		// NOTES PAR THEMES
		$themesUser = $em->createQuery("SELECT IDENTITY(t.theme) as theme, th.name as theme_name FROM MainUserBundle:ThemeUser t JOIN MainThemeBundle:Theme th WHERE t.theme = th.id AND t.user =".$this->container->get('security.context')->getToken()->getUser()->getId())->getResult();
		$i = 0;
		foreach ($themesUser as $themeUser)
		{
			$savoirs_array = array();
			$savoirs_scores_array = array();
			$savoirs = $em->createQuery("SELECT s.id as savoir FROM MainSavoirBundle:Savoir s WHERE s.theme =".$themeUser['theme'])->getResult();
			foreach ($savoirs as $savoir)
				$savoirs_array[] = $savoir['savoir'];
			$savoirs_scores = $em->createQuery("SELECT s.score as score, WEEK(s.date) as week_number FROM MainUserBundle:SavoirUser s WHERE s.savoir IN ('".implode("','",$savoirs_array)."') AND s.date > '".date('Y-m-d', strtotime('-7 week'))."' AND s.user = ".$this->container->get('security.context')->getToken()->getUser()->getId())->getResult();
			foreach ($savoirs_scores as $score)
				$savoirs_scores_array[$score['week_number']][] = $score['score'];
			for($j=0;$j<7;$j++)
			{
				if (isset($savoirs_scores_array[date("W",strtotime('-'.(8-$j).' week'))]))
					$data_notes_by_themes[$i][$j] = array_sum($savoirs_scores_array[date("W",strtotime('-'.(8-$j).' week'))]) / (5*count($savoirs_scores_array[date("W",strtotime('-'.(8-$j).' week'))]));
				else
					$data_notes_by_themes[$i][$j] = 0;
			}
			$i++;
			$legend_notes_by_themes[$i] = $themeUser['theme_name'];
		}
		
		// NOTES PAR SAVOIR VS MEDIANE
		$epreuves = $em->getRepository('MainUserBundle:SavoirUser')->findBy(array("user" => $this->container->get('security.context')->getToken()->getUser()));
		$legend_savoirs = array();
		$notes_savoir = array();
		foreach ($epreuves as $epreuve)
		{
			if (!isset($legend_savoirs[$epreuve->getSavoir()->getId()]))
				$legend_savoirs[$epreuve->getSavoir()->getId()] = $epreuve->getSavoir()->getName();

			if (!isset($notes_savoir[$epreuve->getSavoir()->getId()]) || $notes_savoir[$epreuve->getSavoir()->getId()] < $epreuve->getScore()/5)
				$notes_savoir[$epreuve->getSavoir()->getId()] = $epreuve->getScore()/5;
		}
		
		$savoirs_medianes = $em->createQuery("SELECT AVG(s.score) as score, IDENTITY(s.savoir) as savoir_id FROM MainUserBundle:SavoirUser s WHERE s.savoir IN ('".implode("','",array_keys($legend_savoirs))."') GROUP BY savoir_id")->getResult();
		$notes_medianes_raw = array();
		foreach ($savoirs_medianes as $savoir_mediane)
			$notes_medianes_raw[$savoir_mediane["savoir_id"]] = $savoir_mediane["score"]/5;
			
		$notes_medianes = array();
		foreach ($legend_savoirs as $key => $current_savoir)
			$notes_medianes[] = $notes_medianes_raw[$key];
			
		$legend_savoirs = array_values($legend_savoirs);
		$notes_savoir = array_values($notes_savoir);

		// NOTES
		$classes = $em->getRepository('MainUserBundle:ClasseUser')->findBy(array("user" => $this->container->get('security.context')->getToken()->getUser()));
		$themes = $em->getRepository('MainUserBundle:ThemeUser')->findBy(array("user" => $this->container->get('security.context')->getToken()->getUser()));
		$savoirs = $em->getRepository('MainUserBundle:SavoirUser')->findBy(array("user" => $this->container->get('security.context')->getToken()->getUser()),array('date' => 'DESC'));
		
		
        return $this->render('MainUserBundle:Default:my_stats.html.twig', 
			array('legend' => $legend,'data_epreuve_passees' => $data_epreuve_passees,'data_savoirs_aquis' => $data_savoirs_aquis,
			'data_notes_by_themes' => json_encode($data_notes_by_themes), 'legend_notes_by_themes' => json_encode($legend_notes_by_themes)
			, 'legend_savoirs' => json_encode($legend_savoirs), 'notes_savoir' => json_encode($notes_savoir), 'notes_medianes' => json_encode($notes_medianes)
			, 'classes' => $classes, 'themes' => $themes, 'savoirs' => $savoirs
			));
    }

    public function myStatsProfAction()
    {
        $em = $this->getDoctrine()->getManager();
		$classes = $em->getRepository('MainClasseBundle:Classe')->findBy(array("owner" => $this->container->get('security.context')->getToken()->getUser()));
		foreach ($classes as $classe)
		{
			$classes_array[] = $classe->getId();
		}
		$examens = $em->getRepository('MainClasseBundle:Examen')->findBy(array("classe" => $classes_array));
		$themes = $em->getRepository('MainUserBundle:ThemeUser')->findBy(array("user" => $this->container->get('security.context')->getToken()->getUser()));
		$savoirs = $em->getRepository('MainUserBundle:SavoirUser')->findBy(array("user" => $this->container->get('security.context')->getToken()->getUser()),array('date' => 'DESC'));
		
		return $this->render('MainUserBundle:Default:my_stats_prof.html.twig', 
			array('classes' => $classes, 'examens' => $examens, 'themes' => $themes, 'savoirs' => $savoirs));
		
		
		
	}
	
    public function myStatsProfAjaxAction(Request $request)
    {
		if($this->container->get('request')->isXmlHttpRequest())
		{
			$em = $this->getDoctrine()->getManager();
			$examens_array = array();
			$examens = $em->getRepository('MainClasseBundle:Examen')->findBy(array("classe" => $this->container->get('request')->request->get('classe')));
			foreach ($examens as $examen)
				$examens_array[$examen->getId()] = $examen->getName();
			$json = json_encode($examens_array);
			$response = new Response($json, 200);
			$response->headers->set('Content-Type', 'application/json');
			return $response;
		}
	}
	
    public function myStatsProfAjaxSavoirsAction(Request $request)
    {
		if($this->container->get('request')->isXmlHttpRequest())
		{
			$em = $this->getDoctrine()->getManager();
			$savoirs_array = array();
			$classe = $em->getRepository('MainClasseBundle:Classe')->find($this->container->get('request')->request->get('classe'));
			$savoirs = $em->getRepository('MainSavoirBundle:Savoir')->findBy(array("theme" => $classe->getTheme()));
			foreach ($savoirs as $savoir)
				$savoirs_array[$savoir->getId()] = $savoir->getName();
			$json = json_encode($savoirs_array);
			$response = new Response($json, 200);
			$response->headers->set('Content-Type', 'application/json');
			return $response;
		}
	}
	
    public function myStatsProfAjaxDisplayTableAction(Request $request)
    {
		if($this->container->get('request')->isXmlHttpRequest())
		{
			$em = $this->getDoctrine()->getManager();
			if ($this->container->get('request')->request->get('panel') == 1)
			{
				$eleves_array = array();
				$eleves = $em->getRepository('MainUserBundle:ExamenUser')->findBy(array("examen" => $this->container->get('request')->request->get('examen')),array("score" => "DESC"));
				$template = $this->renderView('MainUserBundle:Default:my_stats_prof_panel1.html.twig', array('eleves'=>$eleves));
				$json = json_encode($template);
				$response = new Response($json, 200);
				$response->headers->set('Content-Type', 'application/json');
				return $response;
			}
			elseif($this->container->get('request')->request->get('panel') == 2)
			{
				$eleves_array = array();
				$eleves = $em->getRepository('MainUserBundle:ExamenUser')->findBy(array("examen" => $this->container->get('request')->request->get('examen')),array("score" => "DESC"));
				$notes = array();
				foreach ($eleves as $eleve)
					$notes[] = $eleve->getScore()/5;
				$data['moyenne'] = array_sum($notes)/count($notes);
				$data['mediane'] = $notes[round(count($notes)/2)];
				$data['haute'] = max($notes);
				$data['basse'] = min($notes);
				$template = $this->renderView('MainUserBundle:Default:my_stats_prof_panel2.html.twig', array('data'=>$data));
				$json = json_encode($template);
				$response = new Response($json, 200);
				$response->headers->set('Content-Type', 'application/json');
				return $response;
			}
			elseif($this->container->get('request')->request->get('panel') == 3)
			{
				$eleves_array = array();
				$savoirs = array();
				$eleves = $em->getRepository('MainUserBundle:ClasseUser')->findBy(array("classe" => $this->container->get('request')->request->get('classe')));
				$savoir_names = $em->createQuery("SELECT s.id, s.name FROM MainSavoirBundle:Savoir s WHERE s.id in (".$this->container->get('request')->request->get('savoirs').")")->getArrayResult();
				foreach ($eleves as $eleve)
					$eleves_array[$eleve->getUser()->getId()] = $eleve->getUser()->getFirstName().' '.$eleve->getUser()->getLastName();
				foreach ($savoir_names as $savoir_name)
					$savoirs[$savoir_name['id']] = $savoir_name['name'];
				$notes = array();
				foreach ($eleves as $eleve)
				{
					$savoirs_user = $em->createQuery("SELECT IDENTITY(s.savoir) as savoir, s.score as score FROM MainUserBundle:SavoirUser s WHERE s.savoir in (".$this->container->get('request')->request->get('savoirs').")")->getArrayResult();
					foreach ($savoirs_user as $savoir_user)
						$notes[$eleve->getUser()->getId()][$savoir_user['savoir']] = $savoir_user['score']/5;
				}
				$template = $this->renderView('MainUserBundle:Default:my_stats_prof_panel3.html.twig', array('eleves'=>$eleves_array,'savoirs'=>$savoirs,'notes'=>$notes));
				$json = json_encode($template);
				$response = new Response($json, 200);
				$response->headers->set('Content-Type', 'application/json');
				return $response;
			}
		}
	}
	
    public function addThemeAction($theme_id)
    {
		$em = $this->getDoctrine()->getManager();
		$matieres = array();

		$matieres = $em->getRepository('MainMatiereBundle:Matiere')->findAll();
		foreach ($matieres as $key => $matiere)
		{
			$matiere_array['id'] = $matiere->getId();
			$matiere_array['name'] = $matiere->getName();
			$matiere_array['themes'] = array();
			$matieres[$key] = $matiere_array;
		}	

		if ($this->container->get('security.context')->isGranted('ROLE_ELEVE'))
		{
			if ($theme_id != NULL)
			{
				$theme = $em->getRepository('MainThemeBundle:Theme')->find($theme_id);
				$theme_user = new ThemeUser();
				$theme_user->setUser($this->container->get('security.context')->getToken()->getUser());
				$theme_user->setTheme($theme);
				$theme_user->setDate(new \DateTime("now"));
				$em->persist($theme_user);
				$em->flush();

				//add a flash
				$this->get('session')->getFlashBag()->add(
					'notice',
					'Le thème '.$theme->getName().' a été ajouté à votre arbre de connaissances avec succès'
				);
			}
		}
		elseif ($this->container->get('security.context')->isGranted('IS_AUTHENTICATED_ANONYMOUSLY') && $theme_id != NULL)
		{
			$session = $this->container->get('session');
			$session->set('theme_id', $theme_id);
			$session->save();
			return $this->forward('MainSavoirBundle:Default:getArbre', array('theme_id'  => $theme_id));
		}
		
		$themes = $em->createQuery("SELECT IDENTITY(t.matiere) as matiere, t.id as id, t.name as name FROM MainThemeBundle:Theme t")->getArrayResult();
		if ($this->container->get('security.context')->isGranted('ROLE_ELEVE'))
			$themes = $em->getRepository('MainUserBundle:ThemeUser')->setAvailable($themes,$this->container->get('security.context')->getToken()->getUser());
		
		foreach ($themes as $theme)
		{
			foreach ($matieres as $key => $matiere)
			{
				$current_matiere = $theme['matiere'];
				if ($current_matiere !== NULL && $current_matiere == $matiere['id'])
					$matieres[$key]['themes'][] = $theme;
			}
		}
		$session = $this->container->get('session');
		$session->set('matieres', $matieres);
		$session->save();
		
        return $this->render('MainUserBundle:Default:add_theme.html.twig', array('matieres' => $matieres));
    }
	
    public function removeThemeAction($theme_id)
    {

		$em = $this->getDoctrine()->getManager();
		if ($theme_id != NULL)
		{
			$theme = $em->getRepository('MainThemeBundle:Theme')->find($theme_id);
			$themeUser = $em->getRepository('MainUserBundle:ThemeUser')->findOneBy(array('theme' => $theme,'user' => $this->container->get('security.context')->getToken()->getUser()));
			if ($themeUser)
				$em->remove($themeUser);
			$em->flush();

			//add a flash
			$this->get('session')->getFlashBag()->add(
				'notice',
				'Le thème '.$theme->getName().' a été supprimé à votre arbre de connaissances avec succès'
			);
		}

		$matieres = array();
		if ($this->container->get('security.context')->isGranted('ROLE_ELEVE'))
		{
			$matieres = $em->getRepository('MainMatiereBundle:Matiere')->findAll();
			foreach ($matieres as $key => $matiere)
			{
				$matiere_array['id'] = $matiere->getId();
				$matiere_array['name'] = $matiere->getName();
				$matiere_array['themes'] = array();
				$matieres[$key] = $matiere_array;
			}	

			$themes = $em->createQuery("SELECT IDENTITY(t.matiere) as matiere, t.id as id, t.name as name FROM MainThemeBundle:Theme t")->getArrayResult();
			$themes = $em->getRepository('MainUserBundle:ThemeUser')->setAvailable($themes,$this->container->get('security.context')->getToken()->getUser());
			foreach ($themes as $theme)
			{
				foreach ($matieres as $key => $matiere)
				{
					$current_matiere = $theme['matiere'];
					if ($current_matiere !== NULL && $current_matiere == $matiere['id'])
						$matieres[$key]['themes'][] = $theme;
				}
			}
			$session = $this->container->get('session');
			$session->set('matieres', $matieres);
			$session->save();
		}
			
		return $this->render('MainUserBundle:Default:add_theme.html.twig', array('matieres' => $matieres));
    }

	public function exportAction()
	{
		$em = $this->getDoctrine()->getManager();
//		$eleves = $em->getRepository('MainUserBundle:User')->findAll();
		$qb = $em->createQueryBuilder();
		$qb->select('u')
            ->from('MainUserBundle:User', 'u')
            ->where('u.roles LIKE :roles')
            ->setParameter('roles', '%ROLE_ELEVE%');
		$eleves =  $qb->getQuery()->getResult();
		
		
		$phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
		$phpExcelObject->getProperties()->setCreator("Bobby")
           ->setLastModifiedBy("Bobby")
           ->setTitle("eSchool user stats export Document")
           ->setSubject("eSchool user stats export Document")
           ->setDescription("This is an export of the users' stats of the eSchool website");
		$objWorksheet = $phpExcelObject->setActiveSheetIndex(0);
		
		$objWorksheet->setCellValueByColumnAndRow(0, 1, "Nom");  
		$objWorksheet->setCellValueByColumnAndRow(1, 1, "Prenom");  
		$objWorksheet->setCellValueByColumnAndRow(2, 1, "Classe");  
		$objWorksheet->setCellValueByColumnAndRow(3, 1, "Nombre de savoirs maitrisés");  
		$objWorksheet->setCellValueByColumnAndRow(4, 1, "Moyenne des savoirs maitrisés");  
		$objWorksheet->setCellValueByColumnAndRow(5, 1, "Progression : nombre de savoirs");  
		$objWorksheet->setCellValueByColumnAndRow(6, 1, "Progression : taux de réussite");  
		
		$row = 2;
		foreach ($eleves as $eleve)
		{
			$query = $em->createQuery(
				'SELECT u.score as score, s.id as id
				FROM MainSavoirBundle:Savoir s, MainUserBundle:SavoirUser u 
				WHERE  u.score >= s.score_mini and u.user = :user_id and u.savoir = s.id'
				)->setParameter('user_id', $eleve->getId());
			$savoirs_maitrises = $query->execute();
			$savoirs_array = array();
			foreach ($savoirs_maitrises as $savoir)
			{
				if (isset($savoirs_array[$savoir["id"]]))
				{
					if ($savoir["score"] > $savoirs_array[$savoir["id"]])
						$savoirs_array[$savoir["id"]] = $savoir["score"];
				}
				else
					$savoirs_array[$savoir["id"]] = $savoir["score"];
			}
			
			$lundi = mktime(0, 0, 0, date('n'), date('j'), date('Y')) - ((date('N')-1)*3600*24);
			$query = $em->createQuery(
				'SELECT u.score as score, s.id as id
				FROM MainSavoirBundle:Savoir s, MainUserBundle:SavoirUser u 
				WHERE  u.score >= s.score_mini and u.user = :user_id and u.savoir = s.id and u.date > :date'
				)
				->setParameter('user_id', $eleve->getId())
				->setParameter('date', date('Y-m-d H:i:s',$lundi));
			$savoirs_semaine = $query->execute();
			$savoirs_semaine_array = array();
			foreach ($savoirs_semaine as $savoir)
			{
				if (isset($savoirs_semaine_array[$savoir["id"]]))
				{
					if ($savoir["score"] > $savoirs_semaine_array[$savoir["id"]])
						$savoirs_semaine_array[$savoir["id"]] = $savoir["score"];
				}
				else
					$savoirs_semaine_array[$savoir["id"]] = $savoir["score"];
			}

			
			$objWorksheet->setCellValueByColumnAndRow(0, $row, $eleve->getLastName());  
			$objWorksheet->setCellValueByColumnAndRow(1, $row, $eleve->getFirstName());  
			$objWorksheet->setCellValueByColumnAndRow(2, $row, $eleve->getClasse());  
			$objWorksheet->setCellValueByColumnAndRow(3, $row, sizeof($savoirs_array));
			if (sizeof($savoirs_array) > 0)
				$objWorksheet->setCellValueByColumnAndRow(4, $row, array_sum($savoirs_array)/sizeof($savoirs_array));  
			$objWorksheet->setCellValueByColumnAndRow(5, $row, sizeof($savoirs_semaine_array));  
			if (sizeof($savoirs_semaine_array) > 0)
				$objWorksheet->setCellValueByColumnAndRow(6, $row, array_sum($savoirs_semaine_array)/sizeof($savoirs_semaine_array));  
			
			$row++;
		}
        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment;filename=eschool-stats-eleve.xls');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');

        return $response;      
		
	}
}
