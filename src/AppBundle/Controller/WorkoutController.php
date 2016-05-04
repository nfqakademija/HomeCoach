<?php
/**
 * Created by PhpStorm.
 * User: darius0021
 * Date: 16.5.1
 * Time: 18.40
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Comments;
use AppBundle\Entity\Workout;
use AppBundle\Form\ActivateType;
use AppBundle\Form\CommentType;
use AppBundle\Form\WorkoutRatingType;
use AppBundle\Form\WorkoutType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\WorkoutHistory;

class WorkoutController extends Controller
{
    /**
     * Create a new Workout
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createWorkoutAction(Request $request)
    {
        $user = $this->getUser();
        if ($user==null) {
            return $this->redirectToRoute('fos_user_security_login');
        }
        $workout = new Workout($user);
        $form = $this->createForm(WorkoutType::class, $workout);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($workout);
            $em->flush();
            return $this->redirect("../workouts/" . $workout->getId());
        }
        return $this->render('@App/Home/createWorkout.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * Shows workout.
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showWorkoutAction($id, Request $request)
    {
        $repo = $this->get('app.repo');
        $workout = $repo->getWorkout($id);

        if (!$workout) {
            throw $this->createNotFoundException(
                //TODO redirect to a proper page with a template
                'No workout found for id '.$id
            );
        }

        $user = $this->getUser();
        $comment = new Comments($user, "");
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $repo->getEntityManager();

            $parent = $this->get('request')->get('parent');
            if ($parent==null) {
                $comment->setWorkout($workout);
                $comments = $workout->getComments();
                $comments[] = $comment;
                $workout->setComments($comments);
                $em->persist($workout);
            } else {
                $parent_comm = $repo->getRepo('AppBundle:Comments')
                    ->find($parent);
                $comment->setParent($parent_comm);
                $comments = $parent_comm->getSubComments();
                $comments[] = $comment;
                $parent_comm->setSubComments($comments);
                $em->persist($parent_comm);
            }
            $em->persist($comment);
            $em->flush();
        }

        $activationForm = null;
        if ($this->getUser() != null) {
            $activationForm = $this->activateWorkout($workout->getId(), $request);
        }

        $formRate = $this->createForm(WorkoutRatingType::class);
        $formRate->handleRequest($request);
        $data = $formRate->get("rating")->getData();
        if (isset($data)) {
            if ($data!=0) {
                $workout->setUserRating($this->getUser(), $data);
                $doc = $repo->getEntityManager();
                $doc->persist($workout);
                $doc->flush();
            }
        }

        return $this->render('@App/Home/queryWorkout.html.twig', array(
            "workout" => $workout,
            "form" => $form->createView(),
            "formRate" => $formRate->createView(),
            "activateForm" => $activationForm
        ));
    }

    /**
     * Handles activation.
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\Form\FormView
     */
    public function activateWorkout($id, Request $request)
    {
        $repo = $this->get('app.repo');
        $workout = $repo->getRepo('AppBundle:Workout')
            ->find($id);
        $disabled = false;
        if ($request->request->has("activateForm")) {
            $disabled=true;
        } elseif ($this->getUser()->getActiveWorkout()!=null) {
            if ($this->getUser()->getActiveWorkout()->getId() == $id) {
                $disabled = true;
            }
        }
        $form = $this->get('form.factory')->createNamed("activateForm", ActivateType::class, null, array(
        'disabled' => $disabled
        ));
        if ($request->request->has("activateForm")) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $user = $this->getUser();
                if ($user != null) {
                    $history = new WorkoutHistory($user, $workout);
                    $user->setActiveWorkout($workout);
                    $user->addWorkoutHistory($history);
                    $em = $repo->getEntityManager();
                    $em->persist($history);
                    $em->persist($user);
                    $em->flush();
                }
            }
        }
        return $form->createView();
    }
}
