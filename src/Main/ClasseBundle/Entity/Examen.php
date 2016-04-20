<?php

namespace Main\ClasseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Examen
 *
 * @ORM\Table(name="examen")
 * @ORM\Entity(repositoryClass="Main\ClasseBundle\Entity\ExamenRepository")
 */
class Examen
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

	/**
	 * @ORM\ManyToOne(targetEntity="Main\ClasseBundle\Entity\Classe")
	 * @ORM\JoinColumn(name="classe", referencedColumnName="id", onDelete="CASCADE")
	*/
	protected $classe;
	
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="debut", type="date")
     */
    private $debut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fin", type="date")
     */
    private $fin;

    /**
     * @var array
     *
     * @ORM\Column(name="savoirs", type="simple_array", nullable=true)
     */
    private $savoirs;

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
     * Set name
     *
     * @param string $name
     * @return Examen
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set debut
     *
     * @param \DateTime $debut
     * @return Examen
     */
    public function setDebut($debut)
    {
        $this->debut = $debut;

        return $this;
    }

    /**
     * Get debut
     *
     * @return \DateTime 
     */
    public function getDebut()
    {
        return $this->debut;
    }

    /**
     * Set fin
     *
     * @param \DateTime $fin
     * @return Examen
     */
    public function setFin($fin)
    {
        $this->fin = $fin;

        return $this;
    }

    /**
     * Get fin
     *
     * @return \DateTime 
     */
    public function getFin()
    {
        return $this->fin;
    }

    /**
     * Set savoirs
     *
     * @param array $savoirs
     * @return Examen
     */
    public function setSavoirs($savoirs)
    {
        $this->savoirs = $savoirs;

        return $this;
    }

    /**
     * Get savoirs
     *
     * @return array 
     */
    public function getSavoirs()
    {
        return $this->savoirs;
    }

    /**
     * Set classe
     *
     * @param \Main\ClasseBundle\Entity\Classe $classe
     * @return Examen
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
}
