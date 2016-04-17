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
 * @ORM\Table(name="Regimes")
 */
class Regime
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
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
     * @ORM\OneToMany(targetEntity="Comments", mappedBy="regime")
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
     * @ORM\OneToMany(targetEntity="UserBundle\Entity\User", mappedBy="active_regime")
     */
    protected $activations;
    /**
     * Regime constructor.
     * @param $creator
     * @param $data_created
     */
    public function __construct($creator, $data_created)
    {
        $this->creator = $creator;
        $this->data_created = $data_created;
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
     * @return Regime
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
    public function getSchedule() {
        return $this->schedule;
    }


    /**
     * Set schedule
     * @param array $schedule
     * @return Regime
     */
    public function setSchedule($schedule) {
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
        if (!isset($this->user_ratings[$user_id]))
            return 0;
        return $this->user_ratings[$user_id];
    }
    /**
     * Set title
     *
     * @param string $title
     *
     * @return Regime
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
     * @return Regime
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
     * @return Regime
     */
    public function setDifficulty($difficulty)
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    /**
     * Set DataCreated
     *
     * @param \DateTime $date
     *
     * @return Regime
     */
    public function setDataCreated ($date)
    {
        $this->data_created = $date;
        return $this;
    }
    /**
     * Get DataCreated
     *
     *
     * @return \DateTime
     */
    public function getDataCreated ()
    {
        return $this->data_created;
    }

    /**
     * Set DataUpdated
     *
     * @param \DateTime $date
     *
     * @return Regime
     */
    public function setDataUpdated ($date)
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
    public function getDataUpdated ()
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

}
