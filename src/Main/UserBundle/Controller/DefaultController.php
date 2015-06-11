<?php

namespace Main\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Main\UserBundle\Entity\ThemeUser as ThemeUser;

class DefaultController extends Controller
{
    public function myHistoryAction()
    {
        $em = $this->getDoctrine()->getManager();
		$epreuves = $em->getRepository('MainUserBundle:SavoirUser')->findByUser($this->container->get('security.context')->getToken()->getUser());

        return $this->render('MainUserBundle:Default:my_history.html.twig', array('epreuves' => $epreuves));
    }

    public function myStatsAction()
    {
        $em = $this->getDoctrine()->getManager();
		//légende pour les jours précédents
		for ($i=1;$i<8;$i++)
			$days[] = strftime("%A",time() + $i*(24 * 60 * 60));
		$legend = json_encode($days);
		
		//epreuves passées
		$epreuves = $em->createQuery("SELECT COUNT(s.id), SUBSTRING(s.date, 1, 10) as day FROM MainUserBundle:SavoirUser s WHERE s.date > '".date('Y-m-d', strtotime('-1 week'))."' GROUP BY day ")->getArrayResult();
		$data_epreuve_passees = array();
		for($i=0;$i<7;$i++)
		{
			foreach ($epreuves as $epreuve)
			{
				if ($epreuve['day'] == date("Y-m-d",strtotime('-'.(6-$i).' days')))
					$data_epreuve_passees[$i] = $epreuve[1];
			}
			if (!isset($data_epreuve_passees[$i]))
			$data_epreuve_passees[$i] = 0;
		}
		$data_epreuve_passees = json_encode($data_epreuve_passees);
		
		//epreuves réussies
		$epreuves_reussies = $em->createQuery("SELECT COUNT(s.id), SUBSTRING(s.date, 1, 10) as day FROM MainUserBundle:SavoirUser s WHERE s.score > 70 AND s.date > '".date('Y-m-d', strtotime('-1 week'))."' GROUP BY day ")->getArrayResult();
		$data_epreuve_reussies = array();
		for($i=0;$i<7;$i++)
		{
			foreach ($epreuves_reussies as $epreuve)
			{
				if ($epreuve['day'] == date("Y-m-d",strtotime('-'.(6-$i).' days')))
					$data_epreuve_reussies[$i] = $epreuve[1];
			}
			if (!isset($data_epreuve_reussies[$i]))
			$data_epreuve_reussies[$i] = 0;
		}
		$data_epreuve_reussies = json_encode($data_epreuve_reussies);

		
        return $this->render('MainUserBundle:Default:my_stats.html.twig', array('legend' => $legend,'data_epreuve_passees' => $data_epreuve_passees,'data_epreuve_reussies' => $data_epreuve_reussies));
    }

    public function addThemeAction($theme_id)
    {
		$em = $this->getDoctrine()->getManager();
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
