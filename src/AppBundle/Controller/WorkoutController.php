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
use AppBundle\Form\WorkoutType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
        $workoutService = $this->get('app.workout_service');
        $workout = new Workout($user);
        
        $form = $this->createForm(WorkoutType::class, $workout);
        if ($workoutService->validateForm($form, $request)) {
            $workoutService->saveWorkout($workout);
            return $this->redirect("../workouts/" . $workout->getId());
        }
        return $this->render('@App/Home/createWorkout.html.twig', array(
            'form' => $form->createView()
        ));
    }
    /**
     * Edit a Workout
     * @param int $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editWorkoutAction($id, Request $request)
    {
        $user = $this->getUser();
        if ($user==null) {
            return $this->redirectToRoute('fos_user_security_login');
        }
        $workoutService = $this->get('app.workout_service');
        $workout = $this->getDoctrine()->getRepository('AppBundle:Workout')->find($id);
        if (!$workoutService->canEdit($user, $workout)) {
            return $this->redirect("../");
        }
        $form = $this->createForm(WorkoutType::class, $workout);
        if ($workoutService->validateForm($form, $request)) {
            $workoutService->saveWorkout($workout);
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
        $workout = $this->getDoctrine()->getRepository('AppBundle:Workout')->find($id);
        if (!$workout) {
            return $this->redirect("../");
        }
        $workoutService = $this->get('app.workout_service');
        $user = $this->getUser();
        $comment = new Comments($user, "");
        //Sukuria visas reikalingas formas.
        $forms = $workoutService->
        createWorkoutForms($user, $workout, $comment, $request, $this->get("form.factory"));
        /**
         * Handlina formas.
         * Jeigu paspaudzia "edit" mygtuka ir turi visas teises, redirectinamas i editWorkout
         */
        if ($workoutService->handleForms($user, $workout, $comment, $forms, $request)==true) {
            return $this->redirect("../editWorkout/" . $workout->getId());
        }
        return $this->render(
            '@App/Home/queryWorkout.html.twig',
            $workoutService->queryOptions($user, $workout, $forms)
        );
    }
}
