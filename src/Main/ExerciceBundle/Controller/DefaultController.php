<?php

namespace Main\ExerciceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use PHPExcel_IOFactory;
use PHPExcel_Cell;
use Main\ExerciceBundle\Entity\Exercice as Exercice;

class DefaultController extends Controller
{
    public function importAction(Request $request)
    {
		$em = $this->getDoctrine()->getManager();
		if ($request->files->get('xlsfile') !== NULL )
		{
			$phpExcelObject = PHPExcel_IOFactory::load($request->files->get('xlsfile')->getRealPath());
			$arraydata = array();
			$success = true;
			$message = "";
			$objWorksheet = $phpExcelObject->setActiveSheetIndex(0);
			$highestRow = $objWorksheet->getHighestRow(); 
			$highestColumn = $objWorksheet->getHighestColumn();  
			$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);  
			for ($row = 2; $row <= $highestRow;++$row) 
			{  
				if ($objWorksheet->getCellByColumnAndRow(0, $row)->getValue() != null)
				{  
					//titre
					$arraydata[$row-1][0]=$objWorksheet->getCellByColumnAndRow(0, $row)->getValue();  
					//énoncé
					$arraydata[$row-1][1]=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();  
					//difficulté
					$arraydata[$row-1][2]=$objWorksheet->getCellByColumnAndRow(2, $row)->getValue();  
					//type
					$arraydata[$row-1][3]=$objWorksheet->getCellByColumnAndRow(3, $row)->getValue();  
					//réponse 1
					$arraydata[$row-1][4]=$objWorksheet->getCellByColumnAndRow(4, $row)->getValue();  
					//réponse 2
					$arraydata[$row-1][5]=$objWorksheet->getCellByColumnAndRow(5, $row)->getValue();  
					//réponse 3
					$arraydata[$row-1][6]=$objWorksheet->getCellByColumnAndRow(6, $row)->getValue();  
					//réponse 4
					$arraydata[$row-1][7]=$objWorksheet->getCellByColumnAndRow(7, $row)->getValue();  
					//Réponse Juste
					$arraydata[$row-1][8]=$objWorksheet->getCellByColumnAndRow(8, $row)->getValue();  
					//Is Numeric Answer
					$arraydata[$row-1][9]=$objWorksheet->getCellByColumnAndRow(9, $row)->getValue();  
					//Init
					$arraydata[$row-1][10]=$objWorksheet->getCellByColumnAndRow(10, $row)->getValue();  
					//Temp
					$arraydata[$row-1][11]=$objWorksheet->getCellByColumnAndRow(11, $row)->getValue();  
					//Savoir
					$arraydata[$row-1][12]=$objWorksheet->getCellByColumnAndRow(12, $row)->getValue();  
					//Thème
					$arraydata[$row-1][13]=$objWorksheet->getCellByColumnAndRow(13, $row)->getValue();  
					//Matière
					$arraydata[$row-1][14]=$objWorksheet->getCellByColumnAndRow(14, $row)->getValue();  
				}
			}
			// var_dump($arraydata);
			foreach ($arraydata as $key => $exercice_TBI)
			{
				$exercice = new Exercice();
				$exercice->setTitre($exercice_TBI[0]);
				$exercice->setTexte($exercice_TBI[1]);
				$exercice->setNiveau($exercice_TBI[2]);
				switch ($exercice_TBI[3])
				{
					//QCM
					case 'QCM':
						$exercice->setType(1);
						break;
					//reponse simple
					case 'Reponse simple':
						$exercice->setType(2);
						break;
					break;
					//texte à trous
					case 'Texte a trous':
						$exercice->setType(3);
						break;
					break;
				}
				$exercice->setReponse1($exercice_TBI[4]);
				$exercice->setReponse2($exercice_TBI[5]);
				$exercice->setReponse3($exercice_TBI[6]);
				$exercice->setReponse4($exercice_TBI[7]);
				$exercice->setReponseJuste(explode ( ",", $exercice_TBI[8]));
				$exercice->setIsNumericAnswer($exercice_TBI[9]);
				$exercice->setInit($exercice_TBI[10]);
				$exercice->setTemp($exercice_TBI[11]);

				$savoir = $em->getRepository('MainSavoirBundle:Savoir')->findOneByName($exercice_TBI[12]);
				if ($savoir!= NULL && $savoir->getTheme() == $exercice_TBI[13] && $savoir->getTheme()->getMatiere() == $exercice_TBI[14])
				{
					$exercice->setSavoir($savoir);
					$em->persist($exercice);
				}
				else
				{
					$message = "problème de savoir/thème/matière à la ligne ".($key+1);
					$success = false;
					break;
				}		
			}		
			if ($success)
				$em->flush();
			return $this->render('MainExerciceBundle:Default:import.html.twig', array('success' => $success,'message' => $message));
		}
		else
			return $this->render('MainExerciceBundle:Default:import.html.twig');
    }

    public function exportAction()
    {
		$em = $this->getDoctrine()->getManager();
		$exercices = $em->getRepository('MainExerciceBundle:Exercice')->findAll();
		$phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
		$phpExcelObject->getProperties()->setCreator("Bobby")
           ->setLastModifiedBy("Bobby")
           ->setTitle("eSchool exercice export Document")
           ->setSubject("eSchool exercice export Document")
           ->setDescription("This is an export of all the exercices of the eSchool website");
		$objWorksheet = $phpExcelObject->setActiveSheetIndex(0);
		
		$objWorksheet->setCellValueByColumnAndRow(0, 1, "Titre");  
		$objWorksheet->setCellValueByColumnAndRow(1, 1, "Enoncé / Question");  
		$objWorksheet->setCellValueByColumnAndRow(2, 1, "Difficulté");  
		$objWorksheet->setCellValueByColumnAndRow(3, 1, "Type d'exercice");  
		$objWorksheet->setCellValueByColumnAndRow(4, 1, "Réponse 1");  
		$objWorksheet->setCellValueByColumnAndRow(5, 1, "Réponse 2");  
		$objWorksheet->setCellValueByColumnAndRow(6, 1, "Réponse 3");  
		$objWorksheet->setCellValueByColumnAndRow(7, 1, "Réponse 4");  
		$objWorksheet->setCellValueByColumnAndRow(8, 1, "Réponse Juste");  
		$objWorksheet->setCellValueByColumnAndRow(9, 1, "Init");  
		$objWorksheet->setCellValueByColumnAndRow(10, 1, "Savoir");  
		$objWorksheet->setCellValueByColumnAndRow(11, 1, "Thème");  
		$objWorksheet->setCellValueByColumnAndRow(12, 1, "Matière");  
		
		$row=2;
		foreach ($exercices  as $exercice)
		{
			$objWorksheet->setCellValueByColumnAndRow(0, $row, $exercice->getTitre());  
			$objWorksheet->setCellValueByColumnAndRow(1, $row, $exercice->getTexte());  
			$objWorksheet->setCellValueByColumnAndRow(2, $row, $exercice->getNiveau());  
			$objWorksheet->setCellValueByColumnAndRow(3, $row, $exercice->getType());  
			$objWorksheet->setCellValueByColumnAndRow(4, $row, $exercice->getReponse1());  
			$objWorksheet->setCellValueByColumnAndRow(5, $row, $exercice->getReponse2());  
			$objWorksheet->setCellValueByColumnAndRow(6, $row, $exercice->getReponse3());  
			$objWorksheet->setCellValueByColumnAndRow(7, $row, $exercice->getReponse4());  
			$objWorksheet->setCellValueByColumnAndRow(8, $row, implode ( "," , $exercice->getReponseJuste() ));  
			$objWorksheet->setCellValueByColumnAndRow(9, $row, $exercice->getInit());  
			$objWorksheet->setCellValueByColumnAndRow(10, $row, $exercice->getSavoir()->getName());  
			$objWorksheet->setCellValueByColumnAndRow(11, $row, $exercice->getSavoir()->getTheme()->getName());  
			$objWorksheet->setCellValueByColumnAndRow(12, $row, $exercice->getSavoir()->getTheme()->getMatiere()->getName());  
			$row++;
		}

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment;filename=eschool-exercices.xls');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');

        return $response;      
    }
}
