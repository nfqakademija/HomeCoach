<?php
/**
 * Created by PhpStorm.
 * User: saulius.vaitkevicius
 * Date: 3/7/2016
 * Time: 3:48 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use UserBundle\Entity\User;

/**
 * @ORM\Entity()
 * @ORM\Table(name="Workouts")
 */
class Workout
{
    const TYPES = ["Jėga", "Ištvermė", "Vikrumas", "Svorio metimas", "Svorio priaugimas"];
    const EQUIPMENTS = ["Kamuolys", "Dviratis", "Vienaratis", "Vienaragis"]; // TODO: Surasyt iranga.
    const MUSCLES = ["Nugara", "Pečiai", "Krūtinė", "Bicepsas", "Tricepsas", "Dilbis", "Pilvo presas", "Kojos"];
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="created_workouts")
     */
    protected $creator;
    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $title;

    /**
     * @ORM\Column(type="integer")
     */
    protected $difficulty;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(min=150, minMessage="Turi būti mažiausiai 150 simboliai")
     */
    protected $description;

    /**
     * @ORM\Column(type="array", nullable=TRUE)
     */
    protected $schedule;

    /**
     * @ORM\Column(type="array", nullable=TRUE)
     */
    protected $user_ratings;

    /**
     * @ORM\Column(type="float", nullable=TRUE)
     */
    protected $rating;

    /**
     * @ORM\OneToMany(targetEntity="Comments", mappedBy="workout")
     */
    protected $comments;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $data_created;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $data_updated;

    /**
     * @var array
     * @ORM\OneToMany(targetEntity="UserBundle\Entity\User", mappedBy="active_workout")
     */
    protected $activations;
    /**
     * @var array(int)
     * @ORM\Column(type="simple_array", nullable=true)
     */
    protected $type;
    /**
     * @var array(int)
     * @ORM\Column(type="simple_array", nullable=true)
     */
    protected $equipment;
    /**
     * @var array(int)
     * @ORM\Column(type="simple_array", nullable=true)
     */
    protected $muscle_group;
    /**
     * Workout constructor.
     * @param $creator
     */
    public function __construct($creator)
    {
        $this->creator = $creator;
        $this->data_created = new \DateTime();
        $this->data_updated = $this->data_created;
        $this->schedule = array (null, null, null, null, null, null, null);
    }


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
     * Get rating
     *
     * @return float
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @return User
     */
    public function getCreator()
    {
        return $this->creator;
    }
    /**
     * Set user rating
     *
     * @param User $user
     * @param integer $user_rating
     *
     * @return Workout
     */
    public function setUserRating($user, $user_rating)
    {
        $user_id = $user->getId();
        $this->user_ratings[$user_id] = $user_rating;

        $sum=0;
        foreach ($this->user_ratings as $i) {
            $sum+=$i;
        }
        $this->rating=round($sum/count($this->user_ratings), 2);

        return $this;
    }

    /**
     * Get schedule
     * @return array
     */
    public function getSchedule()
    {
        return $this->schedule;
    }


    /**
     * Set schedule
     * @param array $schedule
     * @return Workout
     */
    public function setSchedule($schedule)
    {
        $this->schedule = $schedule;
        return $this;
    }


    /**
     * Get rating
     *
     * @param integer $user_id
     *
     * @return integer
     */
    public function getUserRating($user_id)
    {
        if (!isset($this->user_ratings[$user_id])) {
            return 0;
        }
        return $this->user_ratings[$user_id];
    }
    /**
     * Set title
     *
     * @param string $title
     *
     * @return Workout
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set describtion
     *
     * @param string $description
     *
     * @return Workout
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get describtion
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
    /**
     * Get difficulty
     *
     * @return string
     */
    public function getDifficulty()
    {
        return $this->difficulty;
    }

    /**
     * Set difficulty
     *
     * @param integer $difficulty
     *
     * @return Workout
     */
    public function setDifficulty($difficulty)
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    /**
     * Get DataCreated
     *
     *
     * @return \DateTime
     */
    public function getDataCreated()
    {
        return $this->data_created;
    }

    /**
     * Set DataUpdated
     *
     * @param \DateTime $date
     *
     * @return Workout
     */
    public function setDataUpdated($date)
    {
        $this->data_updated = $date;
        return $this;
    }
    /**
     * Get DataUpdated
     *
     *
     * @return \DateTime
     */
    public function getDataUpdated()
    {
        return $this->data_updated;
    }

    /**
     * @return array
     */
    public function getUserRatings()
    {
        return $this->user_ratings;
    }
    /**
     * @return array
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param array $comments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }
    /**
     * @param Comments $comments
     */
    public function addComment($comments)
    {
        $this->comments[] = $comments;
    }

    /**
     * @return array
     */
    public function getActivations()
    {
        return $this->activations;
    }

    /**
     * @param array $activations
     */
    public function setActivations($activations)
    {
        $this->activations = $activations;
    }
    /**
     * @return array
     */
    public function getType()
    {
        return $this->type;
    }
    /**
     * @param array $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }
    /**
     * @return array
     */
    public function getEquipment()
    {
        return $this->equipment;
    }
    /**
     * @param array $equipment
     */
    public function setEquipment($equipment)
    {
        $this->equipment = $equipment;
    }
    /**
     * @return array
     */
    public function getMuscleGroup()
    {
        return $this->muscle_group;
    }
    /**
     * @param array $muscle_group
     */
    public function setMuscleGroup($muscle_group)
    {
        $this->muscle_group = $muscle_group;
    }
    /**
     * @return array
     */
    public function getTypeStrings()
    {
        $types = [];
        if ($this->type!=null) {
            foreach ($this->type as $i) {
                $types[] = self::TYPES[$i];
            }
        }
        return $types;
    }
    /**
     * @return array
     */
    public function getEquipmentStrings()
    {
        $equipment = [];
        if ($this->equipment!=null) {
            foreach ($this->equipment as $i) {
                $equipment[] = self::EQUIPMENTS[$i];
            }
        }
        return $equipment;
    }
    /**
     * @return array
     */
    public function getMuscleGroupStrings()
    {
        $muscles = [];
        if ($this->muscle_group!=null) {
            foreach ($this->muscle_group as $i) {
                $muscles[] = self::MUSCLES[$i];
            }
        }
        return $muscles;
    }
}