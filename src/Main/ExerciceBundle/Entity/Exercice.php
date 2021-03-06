<?php

namespace Main\ExerciceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Exercice
 *
 * @ORM\Table(name="exercice")
 * @ORM\Entity(repositoryClass="Main\ExerciceBundle\Entity\ExerciceRepository")
 * @ORM\HasLifecycleCallbacks
*/
class Exercice
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
	 * @ORM\ManyToOne(targetEntity="Main\UserBundle\Entity\User", cascade={"remove"})
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	*/
	protected $user;

    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=255)
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="texte", type="text")
     */
    private $texte;

    /**
     * @var integer
     *
     * @ORM\Column(name="niveau", type="integer")
     */
    private $niveau;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer")
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="init", type="text", nullable=true)
     */
    private $init;

    /**
     * @var string
     *
     * @ORM\Column(name="temp", type="text", nullable=true)
     */
    private $temp;
	
    /**
     * @var string
     *
     * @ORM\Column(name="reponse1", type="string", length=255)
     */
    private $reponse1;

    /**
     * @var string
     *
     * @ORM\Column(name="reponse2", type="string", length=255, nullable=true)
     */
    private $reponse2;

    /**
     * @var string
     *
     * @ORM\Column(name="reponse3", type="string", length=255, nullable=true)
     */
    private $reponse3;

    /**
     * @var string
     *
     * @ORM\Column(name="reponse4", type="string", length=255, nullable=true)
     */
    private $reponse4;

    /**
     * @var simple_array
     *
     * @ORM\Column(name="reponse_juste", type="simple_array", nullable=true)
     */
    private $reponseJuste;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_numeric_answer", type="boolean", nullable=true)
     */
    private $isNumericAnswer;

	/**
	 * @ORM\ManyToOne(targetEntity="Main\SavoirBundle\Entity\Savoir")
	 * @ORM\JoinColumn(name="savoir_id", referencedColumnName="id", onDelete="CASCADE")
	*/
	protected $savoir;

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
     * @return Exercice
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
     * @return Exercice
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
     * Set reponse1
     *
     * @param string $reponse1
     * @return Exercice
     */
    public function setReponse1($reponse1)
    {
        $this->reponse1 = $reponse1;

        return $this;
    }

    /**
     * Get reponse1
     *
     * @return string 
     */
    public function getReponse1()
    {
        return $this->reponse1;
    }

    /**
     * Set reponse2
     *
     * @param string $reponse2
     * @return Exercice
     */
    public function setReponse2($reponse2)
    {
        $this->reponse2 = $reponse2;

        return $this;
    }

    /**
     * Get reponse2
     *
     * @return string 
     */
    public function getReponse2()
    {
        return $this->reponse2;
    }

    /**
     * Set reponse3
     *
     * @param string $reponse3
     * @return Exercice
     */
    public function setReponse3($reponse3)
    {
        $this->reponse3 = $reponse3;

        return $this;
    }

    /**
     * Get reponse3
     *
     * @return string 
     */
    public function getReponse3()
    {
        return $this->reponse3;
    }

    /**
     * Set reponse4
     *
     * @param string $reponse4
     * @return Exercice
     */
    public function setReponse4($reponse4)
    {
        $this->reponse4 = $reponse4;

        return $this;
    }

    /**
     * Get reponse4
     *
     * @return string 
     */
    public function getReponse4()
    {
        return $this->reponse4;
    }

    /**
     * Set reponseJuste
     *
     * @param string $reponseJuste
     * @return Exercice
     */
    public function setReponseJuste($reponseJuste)
    {
        $this->reponseJuste = $reponseJuste;

        return $this;
    }

    /**
     * Get reponseJuste
     *
     * @return string 
     */
    public function getReponseJuste()
    {
        return $this->reponseJuste;
    }

    /**
     * Set user
     *
     * @param \Main\UserBundle\Entity\User $user
     * @return Exercice
     */
    public function setUser(\Main\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Main\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set savoir
     *
     * @param \Main\SavoirBundle\Entity\Savoir $savoir
     * @return Exercice
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

    /**
     * Set niveau
     *
     * @param integer $niveau
     * @return Exercice
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
     * Set type
     *
     * @param integer $type
     * @return Exercice
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
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
        if ($this->filenameForRemove) {
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
        return 'uploads/exercice';
    }	

    /**
     * Set path
     *
     * @param string $path
     * @return Exercice
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
     * @return Exercice
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
     * Set init
     *
     * @param string $init
     * @return Exercice
     */
    public function setInit($init)
    {
        $this->init = $init;

        return $this;
    }

    /**
     * Get init
     *
     * @return string 
     */
    public function getInit()
    {
        return $this->init;
    }

    /**
     * Set temp
     *
     * @param string $temp
     * @return Exercice
     */
    public function setTemp($temp)
    {
        $this->temp = $temp;

        return $this;
    }

    /**
     * Get temp
     *
     * @return string 
     */
    public function getTemp()
    {
        return $this->temp;
    }

    /**
     * Set isNumericAnswer
     *
     * @param boolean $isNumericAnswer
     * @return Exercice
     */
    public function setIsNumericAnswer($isNumericAnswer)
    {
        $this->isNumericAnswer = $isNumericAnswer;

        return $this;
    }

    /**
     * Get isNumericAnswer
     *
     * @return boolean 
     */
    public function getIsNumericAnswer()
    {
        return $this->isNumericAnswer;
    }
}
