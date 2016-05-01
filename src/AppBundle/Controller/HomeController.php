<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comments;
use AppBundle\Entity\Workout;
use AppBundle\Form\CommentType;
use AppBundle\Form\WorkoutRatingType;
use AppBundle\Form\WorkoutType;
use Doctrine\ORM\Query;
use JMS\Serializer\Tests\Fixtures\Doctrine\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Service\Repo;
use UserBundle\Entity\WorkoutHistory;

class HomeController extends Controller
{
    /**
     * Home page index action. 
     * Shows currently popular workouts
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $repo = $this->get('app.repo');
        $workouts = $repo->getHotWorkouts();
        $json_workouts = json_encode($workouts);

        return $this->render('@App/Home/index.html.twig', array(
            'workouts' => $json_workouts
        ));
    }

    /**
     * Create a new Workout
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createWorkoutAction(Request $request)
    {
        $user = $this->getUser();
        if($user==null)
        {
            return new Response("log in"); //TODO pakeisti i normalu puslapi
        }
        $workout = new Workout($user);

        $form = $this->createForm(WorkoutType::class, $workout);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($workout);
            $em->flush();

            return $this->redirectToRoute('app.taskSuccess');
        }

        return $this->render('@App/Home/createWorkout.html.twig', array(
            'form' => $form->createView()
        ));

    }

//    /**
//     * Rates workout.
//     * @return \Symfony\Component\HttpFoundation\Response
//     */
//    public function rateWorkoutAction($id, Request $request)
//    {
//        $repo = $this->get('app.repo');
//        $workout = $repo->getWorkout($id);
//
//        if (!$workout){
//            throw $this->createNotFoundException(
//                'No workout found for id '.$id
//            );
//        }
//        $form = $this->createForm(WorkoutRatingType::class, $workout);
//
//        $form->handleRequest($request);
//        $data = $form->getData();
//        if (isset($data['rating'])) {
//            $workout->setUserRating($this->getUser(), $data['rating']);
//            $doc = $this->getDoctrine()->getManager();
//            $doc->persist($workout);
//            $doc->flush();
//        }
//        return $this->render('@App/Home/rateWorkout.html.twig', array(
//            'form' => $form->createView(), 'workout' => $workout
//        ));
//    }

    public function showWorkoutAction($id, Request $request)
    {
        $repo = $this->get('app.repo');
        $workout = $repo->getWorkout($id);

        if (!$workout){
            throw $this->createNotFoundException(
                'No workout found for id '.$id
            );
        }
        //Komentaru forma imest.
        $user = $this->getUser();
        $comment = new Comments($user, "");

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $parent = $this->get('request')->get('parent');
            if ($parent==null) {
                $comment->setWorkout($workout);
                $comments = $workout->getComments();
                $comments[] = $comment;
                $workout->setComments($comments);
                $em->persist($workout);
            } else {

                $parent_comm = $this->getDoctrine()
                    ->getRepository('AppBundle:Comments')
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

        $formRate = $this->createForm(WorkoutRatingType::class, $workout);

        $formRate->handleRequest($request);
        $data = $formRate->getData();
        if (isset($data->rating)) {
            $workout->setUserRating($this->getUser(), $data->rating);
            $doc = $this->getDoctrine()->getManager();
            $doc->persist($workout);
            $doc->flush();
        }

        return $this->render('@App/Home/queryWorkout.html.twig', array(
            "workout" => $workout,
            "form" => $form->createView(),
            "formRate" => $formRate->createView(),
            "activateForm" => $activationForm
        ));
    }

    /**
     * Displayed after successfully logging in, registering, creating or updating a workout
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function taskSuccessAction()
    {
        //padaryti kad po keliu sekundziu redirectintu i ka tik sukurto workout'o puslapi
        return $this->render('@App/Home/taskSuccess.html.twig', array());
    }
    public function showWorkoutsPageAction($page, $sort, $difficulty, $search) {
        $start = $page*4;
        if($difficulty == 'all')
        {
            $whereState = "";
        }
        else
        {
            $whereState = "WHERE Workouts.difficulty= :diff";
        }

        if($sort=="rating")
        {
            $query = "SELECT Workouts.id,title, Workouts.rating,description, data_created, Workouts.creator_id, Workouts.difficulty, username FROM Workouts 
            LEFT JOIN fos_user ON fos_user.id=Workouts.creator_id " . $whereState . " ORDER BY Workouts.rating DESC LIMIT " . $start . ",4";
        }
        else
        {
            $query = "SELECT Workouts.id,title, Workouts.rating,description, data_created, Workouts.creator_id, Workouts.difficulty, username FROM Workouts 
            LEFT JOIN fos_user ON fos_user.id=Workouts.creator_id " . $whereState . " ORDER BY Workouts.data_created DESC LIMIT " . $start . ",4";
        }

        $stmt = $this->getDoctrine()->getEntityManager()
            ->getConnection()
            ->prepare($query);
        $stmt->bindValue('diff',$difficulty);

        $stmt->execute();

        $workouts = $stmt->fetchAll();

        $serializer = $this->get('jms_serializer');

        $json = $serializer->toArray($workouts);

        return new Response($serializer->serialize($json, 'json'));
    }

    /**
     * Shows most popular coaches in right sidebar of all pages
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showCoachesAction() {
        $repository = $this->getDoctrine()
            ->getRepository('UserBundle:User');

        //reiks pakeisti ta findAll ir implementuoti searcho funkcijas
        $coaches = $repository->findAll();
        $json = json_encode($coaches);

        return $this->render('base.html.twig', array(
            'coaches' => $json
        ));
    }

    /**
     * Used when searching for users
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function browseUsersAction() {
        $repository = $this->getDoctrine()
            ->getRepository('UserBundle:User');

        //reiks pakeisti ta findAll ir implementuoti searcho funkcijas
        $users = $repository->findAll();
        $json = json_encode($users);

        return $this->render('@App/Home/browseUsers.html.twig', array(
            'users' => $json
        ));
    }

    public function showProfileAction($id)
    {
        $user = $this->getDoctrine()->getRepository("UserBundle:User")->find($id);
        return $this->render('@App/Home/showUser.html.twig', array(
            'user'=>$user
        ));
    }

    public function activateWorkout($id, Request $request)
    {
        $workout = $this->getDoctrine()
            ->getRepository('AppBundle:Workout')
            ->find($id);
        $disabled = false;
        $buttonName = "Aktyvuoti";
        if ($request->request->has("activateForm")) {
            $disabled=true;
        }
        if ($this->getUser()->getActiveWorkout()!=null)
            if ($this->getUser()->getActiveWorkout()->getId()==$id) {
                $disabled=true;
            }
        if ($disabled) {
            $buttonName = "Programa aktyvuota!";
        }
        $form = $this->get('form.factory')->createNamedBuilder("activateForm")
            ->add("Aktyvuoti", SubmitType::class, array (
                'disabled'=>$disabled,
                'label'=>$buttonName
            ))
            ->getForm();
        if ($request->request->has("activateForm")) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $user = $this->getUser();
                if ($user != null) {
                    $history = new WorkoutHistory($user, $workout);
                    $user->setActiveWorkout($workout);
                    $user->addWorkoutHistory($history);
                    $doc = $this->getDoctrine()->getManager();
                    $doc->persist($history);
                    $doc->persist($user);
                    $doc->flush();
                }
            }
        }
        return $form->createView();
    }
}
