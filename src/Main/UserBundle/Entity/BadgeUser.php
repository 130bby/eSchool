<?php

namespace Main\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BadgeUser
 *
 * @ORM\Table(name="badge_user")
 * @ORM\Entity(repositoryClass="Main\UserBundle\Entity\BadgeUserRepository")
 */
class BadgeUser
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
	 * @ORM\ManyToOne(targetEntity="Main\BadgeBundle\Entity\Badge")
	 * @ORM\JoinColumn(name="badge_id", referencedColumnName="id")
	*/
	protected $badge;
	
    /**
     * @var integer
     *
     * @ORM\Column(name="niveau", type="integer")
     */
    private $niveau;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var array
     *
     * @ORM\Column(name="themes", type="simple_array", nullable=true)
     */
    private $themes;
	

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
     * Set niveau
     *
     * @param integer $niveau
     * @return BadgeUser
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
     * Set date
     *
     * @param \DateTime $date
     * @return BadgeUser
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
     * @return BadgeUser
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
     * Set badge
     *
     * @param \Main\BadgeBundle\Entity\Badge $badge
     * @return BadgeUser
     */
    public function setBadge(\Main\BadgeBundle\Entity\Badge $badge = null)
    {
        $this->badge = $badge;

        return $this;
    }

    /**
     * Get badge
     *
     * @return \Main\BadgeBundle\Entity\Badge 
     */
    public function getBadge()
    {
        return $this->badge;
    }

    /**
     * Set themes
     *
     * @param array $themes
     * @return BadgeUser
     */
    public function setThemes($themes)
    {
        $this->themes = $themes;

        return $this;
    }

    /**
     * Get themes
     *
     * @return array 
     */
    public function getThemes()
    {
        return $this->themes;
    }

    /**
     * Add theme
     * @param string $theme
     * @return BadgeUser 
     */
    public function setTheme($key,$value)
    {
        $this->themes[$key] = $value;
        return $this;
    }

    /**
     * Remove theme
     * @param int $key
     * @return BadgeUser 
     */
    public function removeTheme($key)
    {
        unset($this->themes[$key]);
    }
	
}
