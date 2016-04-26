<?php

namespace Main\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ExamenUser
 *
 * @ORM\Table(name="examen_user")
 * @ORM\Entity(repositoryClass="Main\UserBundle\Entity\ExamenUserRepository")
 */
class ExamenUser
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
	 * @ORM\ManyToOne(targetEntity="Main\ClasseBundle\Entity\Examen")
	 * @ORM\JoinColumn(name="examen_id", referencedColumnName="id", onDelete="CASCADE")
	*/
	protected $examen;
	
    /**
     * @var integer
     *
     * @ORM\Column(name="score", type="integer")
     */
    private $score;

    /**
     * @var boolean
     *
     * @ORM\Column(name="success", type="boolean")
     */
    private $success;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="temps", type="time")
     */
    private $temps;

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
     * Set score
     *
     * @param integer $score
     * @return ExamenUser
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return integer 
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set temps
     *
     * @param \DateTime $temps
     * @return ExamenUser
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
     * @return ExamenUser
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
     * @return ExamenUser
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
     * Set examen
     *
     * @param \Main\ClasseBundle\Entity\Examen $examen
     * @return ExamenUser
     */
    public function setExamen(\Main\ClasseBundle\Entity\Examen $examen = null)
    {
        $this->examen = $examen;

        return $this;
    }

    /**
     * Get examen
     *
     * @return \Main\ClasseBundle\Entity\Examen 
     */
    public function getExamen()
    {
        return $this->examen;
    }

    /**
     * Set success
     *
     * @param boolean $success
     * @return ExamenUser
     */
    public function setSuccess($success)
    {
        $this->success = $success;

        return $this;
    }

    /**
     * Get success
     *
     * @return boolean 
     */
    public function getSuccess()
    {
        return $this->success;
    }
}
