<?php

namespace Main\EvaluationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Evaluation
 *
 * @ORM\Table(name="evaluation")
 * @ORM\Entity(repositoryClass="Main\EvaluationBundle\Entity\EvaluationRepository")
 */
class Evaluation
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
	 * @ORM\ManyToOne(targetEntity="Main\ThemeBundle\Entity\Theme", inversedBy="evaluation")
	 * @ORM\JoinColumn(name="theme_id", referencedColumnName="id", onDelete="CASCADE")
	*/
	protected $theme;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="niveau", type="integer")
     */
    private $niveau;

    /**
     * @var string
     *
     * @ORM\Column(name="classe", type="string", length=255)
     */
    private $classe;

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
     * @return Evaluation
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
     * Set niveau
     *
     * @param integer $niveau
     * @return Evaluation
     */
    public function setNiveau($niveau)
    {
        $this->niveau = $niveau;

        return $this;
    }

    /**
     * Get niveau
     *
     * @return integer 
     */
    public function getNiveau()
    {
        return $this->niveau;
    }

    /**
     * Set classe
     *
     * @param string $classe
     * @return Evaluation
     */
    public function setClasse($classe)
    {
        $this->classe = $classe;

        return $this;
    }

    /**
     * Get classe
     *
     * @return string 
     */
    public function getClasse()
    {
        return $this->classe;
    }

    /**
     * Set savoirs
     *
     * @param array $savoirs
     * @return Evaluation
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
     * Set theme
     *
     * @param \Main\ThemeBundle\Entity\Theme $theme
     * @return Evaluation
     */
    public function setTheme(\Main\ThemeBundle\Entity\Theme $theme = null)
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Get theme
     *
     * @return \Main\ThemeBundle\Entity\Theme 
     */
    public function getTheme()
    {
        return $this->theme;
    }
}
