<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comments
 *
 * @ORM\Table(name="comments")
 */
class Comments
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    protected $comment;

    /**
     * @ORM\ManyToOne(targetEntity="Comments")
     */
    protected $parent;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     */
    protected $user;

    /**
     * @ORM\ManyToMany(targetEntity="Comments")
     */
    protected $sub_comments;

    /**
     * @ORM\ManyToOne(targetEntity="Workout", inversedBy="comments")
     */
    private $workout;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $data_created;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $data_updated;

    public function __construct($user, $comment)
    {
        $this->user = $user;
        $this->comment = $comment;
        $this->data_created=$this->data_updated=new \DateTime();

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
     * Get comment
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Comments
     */
    public function setComment($comment)
    {
        $this->comment=$comment;
        return $this;
    }

    /**
     * Set subcomment
     * @param integer $id
     * @param integer $comment
     *
     * @return integer
     */
    public function setSubComment($id, $comment)
    {
        $this->sub_comments[$id] = $comment;
    }

    /**
     * @return array
     */
    public function getSubComments()
    {
        return $this->sub_comments;
    }

    /**
     * @param array $sub_comments
     */
    public function setSubComments($sub_comments)
    {
        $this->sub_comments = $sub_comments;
    }

    /**
     * @return integer
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return Comments
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Comments $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
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
     * @return \DateTime
     */
    public function getDataCreated()
    {
        return $this->data_created;
    }

    /**
     * @return \DateTime
     */
    public function getDataUpdated()
    {
        return $this->data_updated;
    }

    /**
     * @param \DateTime $data_created
     */
    public function setDataCreated($data_created)
    {
        $this->data_created = $data_created;
    }

    /**
     * @param \DateTime $data_updated
     */
    public function setDataUpdated($data_updated)
    {
        $this->data_updated = $data_updated;
    }
}
