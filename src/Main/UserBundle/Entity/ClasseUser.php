<?php

namespace Main\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClasseUser
 *
 * @ORM\Table(name="classe_user")
 * @ORM\Entity(repositoryClass="Main\UserBundle\Entity\ClasseUserRepository")
 */
class ClasseUser
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
	 * @ORM\ManyToOne(targetEntity="Main\UserBundle\Entity\User")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
	*/
	protected $user;

	/**
	 * @ORM\ManyToOne(targetEntity="Main\ClasseBundle\Entity\Classe")
	 * @ORM\JoinColumn(name="classe_id", referencedColumnName="id")
	*/
	protected $classe;
	
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

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
     * Set date
     *
     * @param \DateTime $date
     * @return ClasseUser
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set user
     *
     * @param \Main\UserBundle\Entity\User $user
     * @return ClasseUser
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
     * Set classe
     *
     * @param \Main\ClasseBundle\Entity\Classe $classe
     * @return ClasseUser
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
