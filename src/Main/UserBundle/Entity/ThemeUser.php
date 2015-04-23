<?php

namespace Main\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ThemeUser
 *
 * @ORM\Table(name="theme_user")
 * @ORM\Entity(repositoryClass="Main\UserBundle\Entity\ThemeUserRepository")
 */
class ThemeUser
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
	 * @ORM\ManyToOne(targetEntity="Main\ThemeBundle\Entity\Theme")
	 * @ORM\JoinColumn(name="theme_id", referencedColumnName="id")
	*/
	protected $theme;
	
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
     * @return ThemeUser
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
     * Set theme
     *
     * @param \Main\ThemeBundle\Entity\Theme $theme
     * @return ThemeUser
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
     * Set date
     *
     * @param \DateTime $date
     * @return ThemeUser
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
