<?php
// src/UserBundle/Entity/User.php

namespace UserBundle\Entity;

use AppBundle\Entity\Regime;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=20)
     *
     * @Assert\NotBlank(message="Please enter your name.", groups={"Registration", "Profile"})
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=25)
     *
     * @Assert\NotBlank(message="Please enter your surame.", groups={"Registration", "Profile"})
     */
    protected $surname;

    /**
     * @var Regime
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Regime")
     */
    protected $active_regime;

    /**
     * @var array
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Regime")
     */
    protected $regime_history;
    /**
     * Get ID
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set ID
     *
     * @param integer $id
     *
     * @return User
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return User
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
     * Set surname
     *
     * @param string $surname
     *
     * @return User
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @return Regime
     */
    public function getActiveRegime()
    {
        return $this->active_regime;
    }

    /**
     * @param Regime $active_regime
     */
    public function setActiveRegime($active_regime)
    {
        $this->active_regime = $active_regime;
    }

    /**
     * @return array
     */
    public function getRegimeHistory()
    {
        return $this->regime_history;
    }

    /**
     * @param array $regime_history
     */
    public function setRegimeHistory($regime_history)
    {
        $this->regime_history = $regime_history;
    }

}
