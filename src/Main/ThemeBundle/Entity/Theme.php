<?php

namespace Main\ThemeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Theme
 *
 * @ORM\Table(name="theme")
 * @ORM\Entity(repositoryClass="Main\ThemeBundle\Entity\ThemeRepository")
 */
class Theme
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
	 * @ORM\ManyToOne(targetEntity="Main\MatiereBundle\Entity\Matiere")
	 * @ORM\JoinColumn(name="matiere_id", referencedColumnName="id")
	*/
	protected $matiere;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

	public function __toString()
	{
		return $this->name;
	}
	
    /**
     * Set name
     *
     * @param string $name
     * @return Theme
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
     * Set matiere
     *
     * @param \Main\MatiereBundle\Entity\Matiere $matiere
     * @return Theme
     */
    public function setMatiere(\Main\MatiereBundle\Entity\Matiere $matiere = null)
    {
        $this->matiere = $matiere;

        return $this;
    }

    /**
     * Get matiere
     *
     * @return \Main\MatiereBundle\Entity\Matiere 
     */
    public function getMatiere()
    {
        return $this->matiere;
    }
}
