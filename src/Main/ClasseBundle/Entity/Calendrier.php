<?php

namespace Main\ClasseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Classe
 *
 * @ORM\Table(name="calendrier")
 * @ORM\Entity(repositoryClass="Main\ClasseBundle\Entity\CalendrierRepository")
 */
class Calendrier
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
	 * @ORM\ManyToOne(targetEntity="Main\ClasseBundle\Entity\Classe")
	 * @ORM\JoinColumn(name="classe_id", referencedColumnName="id", onDelete="CASCADE")
	*/
	protected $classe;

	/**
	 * @ORM\ManyToOne(targetEntity="Main\SavoirBundle\Entity\Savoir")
	 * @ORM\JoinColumn(name="savoir_id", referencedColumnName="id", onDelete="CASCADE")
	*/
	protected $savoir;
	
	/**
     * @var string
     *
     * @ORM\Column(name="start", type="date")
     */
    private $start;
	
	/**
     * @var string
     *
     * @ORM\Column(name="end", type="date")
     */
    private $end;
	
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
     * Set start
     *
     * @param \DateTime $start
     * @return Calendrier
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get start
     *
     * @return \DateTime 
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set end
     *
     * @param \DateTime $end
     * @return Calendrier
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get end
     *
     * @return \DateTime 
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Set classe
     *
     * @param \Main\ClasseBundle\Entity\Classe $classe
     * @return Calendrier
     */
    public function setClasse(\Main\ClasseBundle\Entity\Classe $classe = null)
    {
        $this->classe = $classe;

        return $this;
    }

    /**
     * Get classe
     *
     * @return \Main\ClasseBundle\Entity\Classe 
     */
    public function getClasse()
    {
        return $this->classe;
    }

    /**
     * Set savoir
     *
     * @param \Main\SavoirBundle\Entity\Savoir $savoir
     * @return Calendrier
     */
    public function setSavoir(\Main\SavoirBundle\Entity\Savoir $savoir = null)
    {
        $this->savoir = $savoir;

        return $this;
    }

    /**
     * Get savoir
     *
     * @return \Main\SavoirBundle\Entity\Savoir 
     */
    public function getSavoir()
    {
        return $this->savoir;
    }
}
