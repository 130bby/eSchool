<?php

namespace Main\ExerciceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ExerciceStats
 *
 * @ORM\Table(name="exercice_stats")
 * @ORM\Entity(repositoryClass="Main\ExerciceBundle\Entity\ExerciceStatsRepository")
 */
class ExerciceStats
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

	/**
	 * @ORM\ManyToOne(targetEntity="Main\ExerciceBundle\Entity\Exercice")
	 * @ORM\JoinColumn(name="exercice_id", referencedColumnName="id", onDelete="CASCADE")
	*/
	protected $exercice;

    /**
     * @var string
     *
     * @ORM\Column(name="taux_reussite", type="string", length=255)
     */
    private $tauxReussite;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbre_passages", type="integer")
     */
    private $nbrePassages;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set tauxReussite
     *
     * @param string $tauxReussite
     * @return ExerciceStats
     */
    public function setTauxReussite($tauxReussite)
    {
        $this->tauxReussite = $tauxReussite;

        return $this;
    }

    /**
     * Get tauxReussite
     *
     * @return string 
     */
    public function getTauxReussite()
    {
        return $this->tauxReussite;
    }

    /**
     * Set nbrePassages
     *
     * @param integer $nbrePassages
     * @return ExerciceStats
     */
    public function setNbrePassages($nbrePassages)
    {
        $this->nbrePassages = $nbrePassages;

        return $this;
    }

    /**
     * Get nbrePassages
     *
     * @return integer 
     */
    public function getNbrePassages()
    {
        return $this->nbrePassages;
    }

    /**
     * Set exercice
     *
     * @param \Main\ExerciceBundle\Entity\Exercice $exercice
     * @return ExerciceStats
     */
    public function setExercice(\Main\ExerciceBundle\Entity\Exercice $exercice = null)
    {
        $this->exercice = $exercice;

        return $this;
    }

    /**
     * Get exercice
     *
     * @return \Main\ExerciceBundle\Entity\Exercice 
     */
    public function getExercice()
    {
        return $this->exercice;
    }
}
