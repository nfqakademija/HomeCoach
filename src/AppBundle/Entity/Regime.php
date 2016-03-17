<?php
/**
 * Created by PhpStorm.
 * User: saulius.vaitkevicius
 * Date: 3/7/2016
 * Time: 3:48 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(type="string", length=100)
     */
    protected $title;

    /**
     * @ORM\Column(type="text")
     */
    protected $describtion;

    /**
     * @ORM\Column(type="array", nullable=TRUE)
     */
    protected $user_ratings;

    /**
     * @ORM\Column(type="float")
     */
    protected $rating;

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
        //Nereikia kiekviena karta per nauja viso skaiciuot, bet nezinau, ar nenukentes tikslumas.
        if (!isset($this->user_ratings[$user_id])) {
            $this->rating = ($this->rating*count($this->user_ratings)+$user_rating)/(count($this->user_ratings)+1);
        } else {
            $this->rating = ($this->rating*count($this->user_ratings)+$user_rating-$this->user_ratings[$user_id])/count($this->user_ratings);
        }
        $this->user_ratings[$user_id] = $user_rating;

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
     * @param string $describtion
     *
     * @return Regime
     */
    public function setDescribtion($describtion)
    {
        $this->describtion = $describtion;

        return $this;
    }

    /**
     * Get describtion
     *
     * @return string
     */
    public function getDescribtion()
    {
        return $this->describtion;
    }
}
