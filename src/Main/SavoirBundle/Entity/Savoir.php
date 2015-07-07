<?php

namespace Main\SavoirBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Savoir
 *
 * @ORM\Table(name="savoir")
 * @ORM\Entity(repositoryClass="Main\SavoirBundle\Entity\SavoirRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Savoir
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
     * @var string
     *
     * @ORM\Column(name="definition", type="text", nullable=true)
     */
    private $definition;
	
    /**
     * @var string
     *
     * @ORM\Column(name="objectifs", type="text", nullable=true)
     */
    private $objectifs;

    /**
     * @var string
     *
     * @ORM\Column(name="exemples", type="text", nullable=true)
     */
    private $exemples;
	
    /**
     * @var string
     *
     * @ORM\Column(name="historique", type="text", nullable=true)
     */
    private $historique;
	
	/**
     * @var string
     *
     * @ORM\Column(name="classe", type="string", length=255, nullable=true)
     */
    private $classe;

	
    /**
     * @var integer
     *
     * @ORM\Column(name="score_mini", type="integer")
     */
    private $score_mini;

	/**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $path;

	/**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated;

	/**
     * @Assert\File(maxSize="6000000")
     */
    public $file;
	
	// propriété utilisé temporairement pour la suppression
    private $filenameForRemove;
	
    /**
     * @var array
     *
     * @ORM\Column(name="prerequis", type="simple_array", nullable=true)
     */
    private $prerequis;

	/**
	 * @ORM\ManyToOne(targetEntity="Main\ThemeBundle\Entity\Theme")
	 * @ORM\JoinColumn(name="theme_id", referencedColumnName="id")
	*/
	protected $theme;


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
     * Get matiere
     *
     * @return \Main\MatiereBundle\Entity\Matiere 
     */
    public function getMatiere()
    {
        return $this->theme->getMatiere();
    }


    /**
     * Set name
     *
     * @param string $name
     * @return Savoir
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
     * Set prerequis
     *
     * @param array $prerequis
     * @return Savoir
     */
    public function setPrerequis($prerequis)
    {
        $this->prerequis = $prerequis;

        return $this;
    }

    /**
     * Get prerequis
     *
     * @return array 
     */
    public function getPrerequis()
    {
        return $this->prerequis;
    }

    /**
     * Set definition
     *
     * @param string $definition
     * @return Savoir
     */
    public function setDefinition($definition)
    {
        $this->definition = $definition;

        return $this;
    }

    /**
     * Get definition
     *
     * @return string 
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * Set objectifs
     *
     * @param string $objectifs
     * @return Savoir
     */
    public function setObjectifs($objectifs)
    {
        $this->objectifs = $objectifs;

        return $this;
    }

    /**
     * Get objectifs
     *
     * @return string 
     */
    public function getObjectifs()
    {
        return $this->objectifs;
    }

    /**
     * Set exemples
     *
     * @param string $exemples
     * @return Savoir
     */
    public function setExemples($exemples)
    {
        $this->exemples = $exemples;

        return $this;
    }

    /**
     * Get exemples
     *
     * @return string 
     */
    public function getExemples()
    {
        return $this->exemples;
    }

    /**
     * Set historique
     *
     * @param string $historique
     * @return Savoir
     */
    public function setHistorique($historique)
    {
        $this->historique = $historique;

        return $this;
    }

    /**
     * Get historique
     *
     * @return string 
     */
    public function getHistorique()
    {
        return $this->historique;
    }

    /**
     * Set theme
     *
     * @param \Main\ThemeBundle\Entity\Theme $theme
     * @return Savoir
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

    /**
     * Set score_mini
     *
     * @param integer $scoreMini
     * @return Savoir
     */
    public function setScoreMini($scoreMini)
    {
        $this->score_mini = $scoreMini;

        return $this;
    }

    /**
     * Get score_mini
     *
     * @return integer 
     */
    public function getScoreMini()
    {
        return $this->score_mini;
    }
	
	/**
	 * Sets file.
	 *
	 * @param UploadedFile $file
	 */
	public function setFile(UploadedFile $file = null)
	{
		$this->file = $file;
	}

	/**
	 * Get file.
	 *
	 * @return UploadedFile
	 */
	public function getFile()
	{
		return $this->file;
	}	
	
	/**
	 * Updates the hash value to force the preUpdate and postUpdate events to fire
	 */
	public function refreshUpdated() {
		$this->setUpdated(new \DateTime("now"));
	}	
	
    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->file) {
            $this->path = $this->file->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->file) {
            return;
        }

        // vous devez lancer une exception ici si le fichier ne peut pas
        // être déplacé afin que l'entité ne soit pas persistée dans la
        // base de données comme le fait la méthode move() de UploadedFile
        $this->file->move($this->getUploadRootDir(), $this->id.'.'.$this->file->guessExtension());

        unset($this->file);
    }

    /**
     * @ORM\PreRemove()
     */
    public function storeFilenameForRemove()
    {
        $this->filenameForRemove = $this->getAbsolutePath();
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        echo $this->filenameForRemove;
		if ($this->filenameForRemove && file_exists($this->filenameForRemove)) {
            unlink($this->filenameForRemove);
        }
    }

    public function getAbsolutePath()
    {
        return null === $this->path ? null : $this->getUploadRootDir().'/'.$this->id.'.'.$this->path;
    }
	
    public function getWebPath()
    {
        return null === $this->path ? null : $this->getUploadDir().'/'.$this->id.'.'.$this->path;
    }

    protected function getUploadRootDir()
    {
        // le chemin absolu du répertoire où les documents uploadés doivent être sauvegardés
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // on se débarrasse de « __DIR__ » afin de ne pas avoir de problème lorsqu'on affiche
        // le document/image dans la vue.
        return 'uploads/savoir';
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Savoir
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Savoir
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set classe
     *
     * @param string $classe
     * @return Savoir
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
}
