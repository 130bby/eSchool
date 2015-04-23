<?php

namespace Main\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EvaluationUser
 *
 * @ORM\Table(name="evaluation_user")
 * @ORM\Entity(repositoryClass="Main\UserBundle\Entity\EvaluationUserRepository")
 */
class EvaluationUser
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
	 * @ORM\ManyToOne(targetEntity="Main\EvaluationBundle\Entity\Evaluation")
	 * @ORM\JoinColumn(name="evaluation_id", referencedColumnName="id")
	*/
	protected $evaluation;
	
    /**
     * @var integer
     *
     * @ORM\Column(name="score", type="integer")
     */
    private $score;

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
     * @return EvaluationUser
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
     * @return EvaluationUser
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
     * @return EvaluationUser
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
     * @return EvaluationUser
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
     * Set evaluation
     *
     * @param \Main\EvaluationBundle\Entity\Evaluation $evaluation
     * @return EvaluationUser
     */
    public function setEvaluation(\Main\EvaluationBundle\Entity\Evaluation $evaluation = null)
    {
        $this->evaluation = $evaluation;

        return $this;
    }

    /**
     * Get evaluation
     *
     * @return \Main\EvaluationBundle\Entity\Evaluation 
     */
    public function getEvaluation()
    {
        return $this->evaluation;
    }
}
