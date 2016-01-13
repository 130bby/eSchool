<?php

namespace Main\ExerciceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ExerciceReport
 *
 * @ORM\Table(name="exercice_report")
 * @ORM\Entity()
 */
class ExerciceReport
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
	 * @ORM\ManyToOne(targetEntity="Main\ExerciceBundle\Entity\Exercice")
	 * @ORM\JoinColumn(name="exercice_id", referencedColumnName="id", onDelete="CASCADE")
	*/
	protected $exercice;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text")
     */
    private $comment;

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
     * Set comment
     *
     * @param string $comment
     * @return ExerciceReport
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set exercice
     *
     * @param \Main\ExerciceBundle\Entity\Exercice $exercice
     * @return ExerciceStats
     */
    public function setExercice(\Main\ExerciceBundle\Entity\Exercice $exercice = null)
    {
        $this->exercice = $exercice;

        return $this;
    }

    /**
     * Get exercice
     *
     * @return \Main\ExerciceBundle\Entity\Exercice 
     */
    public function getExercice()
    {
        return $this->exercice;
    }
}
