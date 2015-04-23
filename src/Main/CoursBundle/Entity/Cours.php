<?php

namespace Main\CoursBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cours
 *
 * @ORM\Table(name="cours")
 * @ORM\Entity(repositoryClass="Main\CoursBundle\Entity\CoursRepository")
 */
class Cours
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
     * @ORM\Column(name="Titre", type="string", length=255)
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="Texte", type="text")
     */
    private $texte;

	/**
	 * @ORM\ManyToOne(targetEntity="Main\SavoirBundle\Entity\Savoir")
	 * @ORM\JoinColumn(name="savoir_id", referencedColumnName="id")
	*/
	protected $savoir;

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
     * Set titre
     *
     * @param string $titre
     * @return Cours
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string 
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set texte
     *
     * @param string $texte
     * @return Cours
     */
    public function setTexte($texte)
    {
        $this->texte = $texte;

        return $this;
    }

    /**
     * Get texte
     *
     * @return string 
     */
    public function getTexte()
    {
        return $this->texte;
    }

    /**
     * Set savoir
     *
     * @param \Main\SavoirBundle\Entity\Savoir $savoir
     * @return Cours
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
