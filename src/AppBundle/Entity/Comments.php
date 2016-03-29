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
     * @ORM\ManyToOne(targetEntity="Regime")
     */
    private $regime;

    public function __construct($user, $comment)
    {
        $this->user = $user;
        $this->comment = $comment;
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
     * @return Regime
     */
    public function getRegime()
    {
        return $this->regime;
    }

    /**
     * @param Regime $regime
     */
    public function setRegime($regime)
    {
        $this->regime = $regime;
    }

}
