<?php

namespace UserBundle\Entity;

use AppBundle\Entity\Workout;
use Doctrine\ORM\Mapping as ORM;

/**
 * WorkoutHistory
 *
 * @ORM\Table(name="workout_history")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\WorkoutHistoryRepository")
 */
class WorkoutHistory
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="workout_history")
     */
    protected $user;
    /**
     * @var Workout
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Workout")
     */
    protected $workout;
    /**
     * @var /DateTime
     * @ORM\Column(type="datetime")
     */
    protected $date;

    /**
     * WorkoutHistory constructor.
     * @param $user
     * @param Workout $workout
     */
    public function __construct($user, Workout $workout)
    {
        $this->user = $user;
        $this->workout = $workout;
        $this->date = new \DateTime();
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return Workout
     */
    public function getWorkout()
    {
        return $this->workout;
    }

    /**
     * @param Workout $workout
     */
    public function setWorkout($workout)
    {
        $this->workout = $workout;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }
}
