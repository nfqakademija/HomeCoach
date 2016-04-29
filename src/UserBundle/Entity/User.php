<?php
// src/UserBundle/Entity/User.php

namespace UserBundle\Entity;

use AppBundle\Entity\Workout;
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
     * @var Workout
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Workout")
     */
    protected $active_workout;

    /**
     * @var array
     * @ORM\OneToMany(targetEntity="WorkoutHistory", mappedBy="user")
     */
    protected $workout_history;

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
     * @return Workout
     */
    public function getActiveWorkout()
    {
        return $this->active_workout;
    }

    /**
     * @param Workout $active_workout
     */
    public function setActiveWorkout($active_workout)
    {
        $this->active_workout = $active_workout;
    }

    /**
     * @return array
     */
    public function getWorkoutHistory()
    {
        return $this->workout_history;
    }

    /**
     * @param array $workout_history
     */
    public function setWorkoutHistory($workout_history)
    {
        $this->workout_history = $workout_history;
    }

    /**
     * Adds one history entity
     * @param WorkoutHistory $history
     */
    public function addWorkoutHistory($history) {
        $this->workout_history[] = $history;
    }
}
