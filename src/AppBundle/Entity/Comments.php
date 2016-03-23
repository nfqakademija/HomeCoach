<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comments
 *
 * @ORM\Table(name="comments")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CommentsRepository")
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
     * @ORM\Column(type="integer")
     */
    protected $user_id;

    /**
     * @ORM\Column(type="array", nullable=TRUE)
     */
    protected $sub_comments;

    public function __construct($user_id, $comment)
    {
        $this->setUser($user_id);
        $this->setComment($comment);
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
     * Set user
     *
     * @param integer $user_id
     *
     * @return Comments
     */
    public function setUser($user_id)
    {
        $this->user_id=$user_id;
        return $this;
    }

    /**
     * Get user id
     * @return integer
     */
    public function getUser()
    {
        return $this->user_id;
    }

    /**
     * Set subcomment
     * @param integer $id
     * @param Comments $comment
     *
     * @return Comments
     */
    public function setSubComment($id, $comment)
    {
        $this->sub_comments[$id] = $comment;
    }

    /**
     * @return mixed
     */
    public function getSubComments()
    {
        return $this->sub_comments;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Get subcomment
     * @param integer $id
     * @return Comments
     */
    public function getSubComment($id)
    {
        return $this->sub_comments[$id];
    }

}
