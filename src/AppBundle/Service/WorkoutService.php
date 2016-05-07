<?php
/**
 * Created by PhpStorm.
 * User: darius0021
 * Date: 16.5.7
 * Time: 16.19
 */

namespace AppBundle\Service;

use AppBundle\Entity\Comments;
use AppBundle\Entity\Workout;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;
use UserBundle\Entity\WorkoutHistory;

class WorkoutService
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * WorkoutService constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param User $user
     * @param Workout $workout
     * @return bool
     */
    public function canEdit($user, $workout)
    {
        if ($user == null || $workout == null) {
            return false;
        }
        if ($workout->getCreator()->getId() == $user->getId()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param Workout $workout
     */
    public function saveWorkout($workout)
    {
        $this->entityManager->persist($workout);
        $this->entityManager->flush();
    }

    /**
     * @param User $user
     * @param Workout $workout
     * @param Request $request
     * @return bool
     */
    public function enableActivation($user, $workout, Request $request)
    {
        $disabled = false;
        if ($request->request->has("activateForm")) {
            $disabled=true;
        } elseif ($user->getActiveWorkout()!=null) {
            if ($user->getActiveWorkout()->getId() == $workout->getId()) {
                $disabled = true;
            }
        }
        return $disabled;
    }

    /**
     * @param User $user
     * @param Workout $workout
     */
    public function activateWorkout($user, $workout)
    {
        $history = new WorkoutHistory($user, $workout);
        $user->setActiveWorkout($workout);
        $user->addWorkoutHistory($history);

        $this->entityManager->persist($history);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * @param User $user
     * @param Workout $workout
     * @param int $score
     */
    public function rateWorkout($user, $workout, $score)
    {
        if (isset($score)) {
            if ($score!=0) {
                $workout->setUserRating($user, $score);
                $this->entityManager->persist($workout);
                $this->entityManager->flush();
            }
        }
    }

    /**
     * @param Workout $workout
     * @param Comments $comment
     */
    public function commentWorkout($workout, $comment)
    {
        $comment->setWorkout($workout);
        $comments = $workout->getComments();
        $comments[] = $comment;
        $workout->setComments($comments);

        $this->entityManager->persist($comment);
        $this->entityManager->persist($workout);
        $this->entityManager->flush();
    }

    /**
     * @param Workout $workout
     */
    public function deleteWorkout($workout)
    {
        $this->entityManager->remove($workout);
        $this->entityManager->flush();
    }
}
