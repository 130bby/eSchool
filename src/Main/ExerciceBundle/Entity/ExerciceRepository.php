<?php

namespace Main\ExerciceBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ExerciceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ExerciceRepository extends EntityRepository
{

	public function getExercicesEpreuve($savoir,$epreuve = true)
	{
		$exercices_all = $this->getEntityManager()
				->createQuery('SELECT e FROM MainExerciceBundle:Exercice e WHERE e.savoir ='.$savoir->getId())->getArrayResult();
		$exercices_simple = array();
		$exercices_moyen = array();
		$exercices_difficile = array();
		$exercices_tres_difficile = array();
		$exercices = array();
		foreach ($exercices_all as $exercice)
		{
			switch ($exercice['niveau'])
			{
				case 1:
					$exercices_simple[] = $exercice;
				break;
				case 2:
					$exercices_moyen[] = $exercice;
				break;
				case 3:
					$exercices_difficile[] = $exercice;
				break;
				case 4:
					$exercices_tres_difficile[] = $exercice;
				break;
			}
		}
		shuffle ($exercices_simple);
		shuffle ($exercices_moyen);
		shuffle ($exercices_difficile);
		shuffle ($exercices_tres_difficile);
		
		if ($epreuve == true)
		{
			if (!empty($exercices_simple))
				$exercices = array_merge($exercices, array_slice ( $exercices_simple , 0 , min(10,sizeof($exercices_simple))  ));
			if (!empty($exercices_moyen))
				$exercices = array_merge($exercices, array_slice ( $exercices_moyen , 0 , min(5,sizeof($exercices_moyen))  ));
			if (!empty($exercices_difficile))
				$exercices = array_merge($exercices, array_slice ( $exercices_difficile , 0 , min(3,sizeof($exercices_difficile))  ));
			if (!empty($exercices_tres_difficile))
				$exercices = array_merge($exercices, array_slice ( $exercices_tres_difficile , 0 , min(2,sizeof($exercices_tres_difficile))  ));
		}
		else
		{
			if (!empty($exercices_simple))
				$exercices = array_merge($exercices, array_slice ( $exercices_simple , 0 , min(3,sizeof($exercices_simple))  ));
			if (!empty($exercices_moyen))
				$exercices = array_merge($exercices, array_slice ( $exercices_moyen , 0 , min(2,sizeof($exercices_moyen))  ));
			if (!empty($exercices_difficile))
				$exercices = array_merge($exercices, array_slice ( $exercices_difficile , 0 , min(1,sizeof($exercices_difficile))  ));
		}
			
		shuffle ($exercices);
		$this->handleRandomExercices($exercices);
		return $exercices;
	}
	
	public function getReponseExercice($exercice,$type_exo,$reponses,$reponse_juste,$enonce)
	{
		switch ($exercice->getType())
		{
			//QCM
			case 1:
				if ($reponses == $exercice->getReponseJuste())
					return true;
				else
					return array(false,$reponse_juste);
			break;
			//reponse simple
			case 2:
				if ($reponses[0] == $reponse_juste)
					return true;
				else
					return array(false,$reponse_juste);
			break;
			//texte à trous
			case 3:
				$success = true;
				//attention, faille possible sur le split à la virgule
				//a refaire, avec des for ?
				$reponses_justes = explode(",",$reponse_juste);
				if ($reponses_justes[0] != "" && isset($reponses[0]) && html_entity_decode($reponses[0]) != $reponses_justes[0])
					$success = false;
				if ($reponses_justes[1] != "" && isset($reponses[1]) && html_entity_decode($reponses[1]) != $reponses_justes[1])
					$success = false;
				if ($reponses_justes[2] != "" && isset($reponses[2]) && html_entity_decode($reponses[2]) != $reponses_justes[2])
					$success = false;
				if ($reponses_justes[3] != "" && isset($reponses[3]) && html_entity_decode($reponses[3]) != $reponses_justes[3])
					$success = false;
				if ($success !== true)
				{
					$exercice_txt = str_replace("…", "...", $enonce);
					$texte_array = explode("...",$exercice_txt);
					$texte_reponse = $texte_array[0];
					if (sizeof($texte_array)>1)
						$texte_reponse .= " ".$reponses_justes[0]." ".$texte_array[1];
					if (sizeof($texte_array)>2)
						$texte_reponse .= " ".$reponses_justes[1]." ".$texte_array[2];
					if (sizeof($texte_array)>3)
						$texte_reponse .= " ".$reponses_justes[2]." ".$texte_array[3];
					if (sizeof($texte_array)>4)
						$texte_reponse .= " ".$reponses_justes[3]." ".$texte_array[4];
					
					return array(false,$texte_reponse);
				}
				else
					return $success;
			break;
		}
	}
	

	public function handleRandomExercices(&$exercices)
	{
		foreach ($exercices as $key => $exercice)
		{
			if ($exercice['init'] != NULL)
			{
				// echo "oui !";
				$init_array = explode(';',$exercice['init']);
				foreach ($init_array as $variable_full)
				{
					if ($variable_full != NULL)
					{
						$nom_variable = explode('=',trim($variable_full));
						$chiffres_variable = preg_split('/[(),]/', $nom_variable[1]);
						if ((int)$chiffres_variable[3] != 0)
							$valeur_variable = (float)rand((int)$chiffres_variable[1]*pow(10, (int)$chiffres_variable[3]),(int)$chiffres_variable[2]*pow(10, (int)$chiffres_variable[3]))/pow(10, (int)$chiffres_variable[3]);
						else
							$valeur_variable = rand((int)$chiffres_variable[1],(int)$chiffres_variable[2]);
						$enonce = explode('&',$exercice['texte']);
						$i=0;
						foreach ($enonce as $cle => $enonce_part)
						{
							if ($i % 2 == 1 && $enonce_part == $nom_variable[0])
								$exercices[$key]['texte'] = str_replace('&'.$nom_variable[0].'&',$valeur_variable,$exercices[$key]['texte']);
							$i++;
						}

						$reponse1 = explode('&',$exercice['reponse1']);
						$i=0;
						foreach ($reponse1 as $cle => $reponse_part)
						{
							if ($i % 2 == 1 && $reponse_part == $nom_variable[0])
								$exercices[$key]['reponse1'] = str_replace('&'.$nom_variable[0].'&',$valeur_variable,$exercices[$key]['reponse1']);
							$i++;
						}
						if($exercice['reponse2'] != NULL)
						{
							$reponse2 = explode('&',$exercice['reponse2']);
							$i=0;
							foreach ($reponse2 as $cle => $reponse_part)
							{
								if ($i % 2 == 1 && $reponse_part == $nom_variable[0])
									$exercices[$key]['reponse2'] = str_replace('&'.$nom_variable[0].'&',$valeur_variable,$exercices[$key]['reponse2']);
								$i++;
							}
						}
						if($exercice['reponse3'] != NULL)
						{
							$reponse3 = explode('&',$exercice['reponse3']);
							$i=0;
							foreach ($reponse3 as $cle => $reponse_part)
							{
								if ($i % 2 == 1 && $reponse_part == $nom_variable[0])
									$exercices[$key]['reponse3'] = str_replace('&'.$nom_variable[0].'&',$valeur_variable,$exercices[$key]['reponse3']);
								$i++;
							}
						}
						if($exercice['reponse4'] != NULL)
						{
							$reponse4 = explode('&',$exercice['reponse4']);
							$i=0;
							foreach ($reponse4 as $cle => $reponse_part)
							{
								if ($i % 2 == 1 && $reponse_part == $nom_variable[0])
									$exercices[$key]['reponse4'] = str_replace('&'.$nom_variable[0].'&',$valeur_variable,$exercices[$key]['reponse4']);
								$i++;
							}
						}
						if($exercice['temp'] != NULL)
						{
							$temp = explode('&',$exercice['temp']);
							$i=0;
							foreach ($temp as $cle => $temp_part)
							{
								if ($i % 2 == 1 && $temp_part == $nom_variable[0])
								{
									$exercices[$key]['temp'] = str_replace('&'.$nom_variable[0].'&',$valeur_variable,$exercices[$key]['temp']);
								}
								$i++;
							}
						}
					}
				}
				
				//gestion des variables temporaires, on les remplacde par leur valeur dans l'enonce et les reponses
				$temp_array = explode(';',trim($exercices[$key]['temp']));
				foreach ($temp_array as $variable_temp_full)
				{
					$variable = explode('=',$variable_temp_full);
					if (isset($variable[1]))
					{
						$str = preg_replace('/([0-9]+|\([0-9\+\-\*\/]+\)|[^\+\-\*\/]\([\S]+\))\^([0-9]+|\([0-9\+\-\*\/]+\)|[^\+\-\*\/]\([\S]+\))/', 'pow($1,$2)', $variable[1]);
						$variable[1] = eval('return '.$str.';');
					}	
					$enonce = explode('&',$exercices[$key]['texte']);
					$i=0;
					foreach ($enonce as $cle => $enonce_part)
					{
						if ($i % 2 == 1 && $enonce_part == $variable[0] && $variable[0] != "")
						{
							$exercices[$key]['texte'] = str_replace('&'.$variable[0].'&',$variable[1],$exercices[$key]['texte']);
						}
						$i++;
					}

					$reponse1 = explode('&',$exercices[$key]['reponse1']);
					$i=0;
					foreach ($reponse1 as $cle => $reponse_part)
					{
						if ($i % 2 == 1 && $reponse_part == $variable[0])
						{
							$exercices[$key]['reponse1'] = str_replace('&'.$variable[0].'&',$variable[1],$exercices[$key]['reponse1']);
						}
						$i++;
					}

					if($exercices[$key]['reponse2'] != NULL)
					{
						$reponse2 = explode('&',$exercices[$key]['reponse2']);
						$i=0;
						foreach ($reponse2 as $cle => $reponse_part)
						{
							if ($i % 2 == 1 && $reponse_part == $variable[0])
								$exercices[$key]['reponse2'] = str_replace('&'.$variable[0].'&',$variable[1],$exercices[$key]['reponse2']);
							$i++;
						}
					}
					if($exercices[$key]['reponse3'] != NULL)
					{
						$reponse3 = explode('&',$exercices[$key]['reponse3']);
						$i=0;
						foreach ($reponse3 as $cle => $reponse_part)
						{
							if ($i % 2 == 1 && $reponse_part == $variable[0])
								$exercices[$key]['reponse3'] = str_replace('&'.$variable[0].'&',$variable[1],$exercices[$key]['reponse3']);
							$i++;
						}
					}
					if($exercices[$key]['reponse4'] != NULL)
					{
						$reponse4 = explode('&',$exercices[$key]['reponse4']);
						$i=0;
						foreach ($reponse4 as $cle => $reponse_part)
						{
							if ($i % 2 == 1 && $reponse_part == $variable[0])
								$exercices[$key]['reponse4'] = str_replace('&'.$variable[0].'&',$variable[1],$exercices[$key]['reponse4']);
							$i++;
						}
					}
				}
				if ($exercices[$key]['isNumericAnswer'])
				{
					$exercices[$key]['reponse1'] = eval('return '.$exercices[$key]['reponse1'].';');
					$exercices[$key]['reponse2'] = eval('return '.$exercices[$key]['reponse2'].';');
					$exercices[$key]['reponse3'] = eval('return '.$exercices[$key]['reponse3'].';');
					$exercices[$key]['reponse4'] = eval('return '.$exercices[$key]['reponse4'].';');
				}
			}
		}
	
	}
}
