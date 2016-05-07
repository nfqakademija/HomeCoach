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
use AppBundle\Form\ActivateType;
use AppBundle\Form\CommentType;
use AppBundle\Form\WorkoutEditType;
use AppBundle\Form\WorkoutRatingType;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;
use UserBundle\Entity\WorkoutHistory;

class WorkoutService
{
    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    /**
     * WorkoutService constructor.
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager
     */
    private function getEntityManager()
    {
        return $this->managerRegistry->getManager();
    }
    /**
     * @param null|User $user
     * @param Workout $workout
     * @param Comments $comment
     * @param Request $request
     * @param FormFactory $formFactory
     * @return array
     */
    public function createWorkoutForms($user, $workout, $comment, $request, $formFactory)
    {
        $forms = [];
        if ($user != null) {
            $forms["commentForm"] = $formFactory->createNamed("commentForm", CommentType::class, $comment);
            $forms["activateForm"] = $formFactory->createNamed("activateForm", ActivateType::class, null, array(
                'disabled' => $this->enableActivation($user, $workout, $request)
            ));
            $forms["rateForm"] = $formFactory->create(WorkoutRatingType::class, null);
        }
        if ($this->canEdit($user, $workout)) {
            $forms["editForm"] = $formFactory->createNamed("editForm", WorkoutEditType::class, null);
        }
        return $forms;
    }
    /**
     * @param User $user
     * @param Workout $workout
     * @param Comments $comment
     * @param array $forms
     * @param Request $request
     * @return bool
     */
    public function handleForms($user, $workout, $comment, $forms, $request)
    {
        if ($this->validateForm($forms["commentForm"], $request)) {
            $this->commentWorkout($workout, $comment);
        }
        if ($this->validateForm($forms["activateForm"], $request)) {
            $this->activateWorkout($user, $workout);
        }
        if ($forms["rateForm"] != null) {
            $forms["rateForm"]->handleRequest($request);
            $this->rateWorkout($user, $workout, $forms["rateForm"]->get("rating")->getData());
        }
        if ($this->validateForm($forms["editForm"], $request) &&
            $forms["editForm"]->getClickedButton()->getName()=="edit" &&
            $this->canEdit($user, $workout)) {
            return true;
        }
        return false;
    }
    /**
     * @param User $user
     * @param Workout $workout
     * @param array $forms
     * @return array
     */
    public function queryOptions($user, $workout, $forms)
    {
        $options = [];
        $options["workout"] = $workout;
        if ($user != null) {
            $options["form"] = $forms["commentForm"]->createView();
            $options["formRate"] = $forms["rateForm"]->createView();
            $options["activateForm"] = $forms["activateForm"]->createView();
        } else {
            $options["form"] = $options["formRate"] = $options["activateForm"] = null;
        }
        if ($this->canEdit($user, $workout)) {
            $options["editForm"] = $forms["editForm"]->createView();
        } else {
            $options["editForm"] = null;
        }
        return $options;
    }

    /**
     * @param Form $form
     * @param Request $request
     * @return bool
     */
    public function validateForm($form, Request $request)
    {
        if ($request->request->has($form->getName())) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                return true;
            }
        }
        return false;
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
        $this->getEntityManager()->persist($workout);
        $this->getEntityManager()->flush();
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

        $this->getEntityManager()->persist($history);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
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
                $this->getEntityManager()->persist($workout);
                $this->getEntityManager()->flush();
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

        $this->getEntityManager()->persist($comment);
        $this->getEntityManager()->persist($workout);
        $this->getEntityManager()->flush();
    }
}
