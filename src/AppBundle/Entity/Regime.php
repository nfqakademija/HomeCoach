<?php
/**
 * Created by PhpStorm.
 * User: saulius.vaitkevicius
 * Date: 3/7/2016
 * Time: 3:48 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;

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
     * @ORM\Column(type="integer")
     */
    protected $creator_id;
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
     * @ORM\Column(type="array", nullable=TRUE)
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
     * Regime constructor.
     * @param $creator_id
     * @param $data_created
     */
    public function __construct($creator_id, $data_created)
    {
        $this->creator_id = $creator_id;
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
     * Set user rating
     *
     * @param integer $user_id
     * @param integer $user_rating
     *
     * @return Regime
     */
    public function setUserRating($user_id, $user_rating)
    {

        if (!isset($this->user_ratings[$user_id])) {
            $this->rating = ($this->rating*count($this->user_ratings)+$user_rating)/(count($this->user_ratings)+1);
        } else {
            $this->rating = ($this->rating*count($this->user_ratings)+$user_rating-$this->user_ratings[$user_id])/count($this->user_ratings);
        }
        $this->user_ratings[$user_id] = $user_rating;

        return $this;
    }

    /**
     * Get day schedule
     *
     *
     *
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
     * Get comment
     *
     * @param integer $index
     *
     * @return Comments
     */
    public function getComment($index)
    {
        return $this->comments[$index];
    }

    /**
     * Set comment
     *
     * @param integer $index
     * @param Comments $comment
     * @return Regime
     */
    public function setComment($index, $comment)
    {
        $this->comments[$index]=$comment;
        return $this;
    }

    /**
     * Get comment
     *
     * @param Comments $comment
     *
     * @return Regime
     */
    public function createComment($comment)
    {
        $this->comments[count($this->comments)]=$comment;
        return $this;
    }
    /**
     * Set DataCreated
     *
     * @param DateTime $date
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
     * @return DateTime
     */
    public function getDataCreated ()
    {
        return $this->data_created;
    }

    /**
     * Set DataUpdated
     *
     * @param DateTime $date
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
     * @return DateTime
     */
    public function getDataUpdated ()
    {
        return $this->data_updated;
    }

    /**
     * @return User
     */
    public function getUser ()
    {
       return $this->creator_id;
    }

    /**
     * @return mixed
     */
    public function getComments()
    {
        return $this->comments;
    }
}
