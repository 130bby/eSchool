<?php

namespace Main\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SavoirUser
 *
 * @ORM\Table(name="savoir_user")
 * @ORM\Entity(repositoryClass="Main\UserBundle\Entity\SavoirUserRepository")
 */
class SavoirUser
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
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	*/
	protected $user;

	/**
	 * @ORM\ManyToOne(targetEntity="Main\SavoirBundle\Entity\Savoir")
	 * @ORM\JoinColumn(name="savoir_id", referencedColumnName="id")
	*/
	protected $savoir;
	
    /**
     * @var float
     *
     * @ORM\Column(name="score", type="float")
     */
    private $score;
	
    /**
     * @var time
     *
     * @ORM\Column(name="temps", type="time")
     */
    private $temps;

    /**
     * @var datetime
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
     * Set user
     *
     * @param \Main\UserBundle\Entity\User $user
     * @return SavoirUser
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
     * @return SavoirUser
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
     * Set score
     *
     * @param float $score
     * @return SavoirUser
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return float 
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set temps
     *
     * @param \DateTime $temps
     * @return SavoirUser
     */
    public function setTemps($temps)
    {
        $this->temps = $temps;

        return $this;
    }

    /**
     * Get temps
     *
     * @return \DateTime 
     */
    public function getTemps()
    {
        return $this->temps;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return SavoirUser
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
}
