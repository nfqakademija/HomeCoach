<?php
namespace AppBundle\Controller;
use AppBundle\Entity\Comments;
use AppBundle\Entity\Workout;
use Doctrine\ORM\Query;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Model\UserInterface;
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
        /*
        $repo = $this->get('app.repo');
        $workouts = $repo->getHotWorkouts();
        //TODO kadangi vistiek darom su angularu, tai grazinti tiesiog response, o ne render()
        */
        return $this->render('@App/Home/index.html.twig'
        );
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
            return new Response("log in");
        }
        $workout = new Workout($user, new \DateTime());
        $workout->setDataUpdated($workout->getDataCreated());
        $form = $this->createFormBuilder($workout)
            ->add('title', TextType::class)
            ->add('difficulty', ChoiceType::class, array(
                'choices' => array(
                    1   => 'Labai lengva',
                    2   => 'Lengva',
                    3   => 'Vidutine',
                    4   => 'Sunki',
                    5   => 'Labai sunki'
                ), 'expanded' => true,
            ))
            ->add('description', TextareaType::class)
            ->getForm();
        $schedule = array (null, null, null, null, null, null, null);
        $workout->setSchedule($schedule);
        $form->add('schedule', CollectionType::class, array(
            'entry_type' => TextareaType::class,
            'required' => false,
        ));
        $form->add('type', ChoiceType::class, array(
            'choices' => Workout::TYPES,
            'expanded' => true,
            'multiple' => true
        ))->add('equipment', ChoiceType::class, array(
            'choices' => Workout::EQUIPMENTS,
            'expanded' => true,
            'multiple' => true
        ))->add('muscle_group', ChoiceType::class, array(
            'choices' => Workout::MUSCLES,
            'expanded' => true,
            'multiple' => true
        ));
        $form->add('save', SubmitType::class, array('label' => 'Pridėti programą'));
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
    /**
     * Rates workout.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rateWorkoutAction($id, Request $request)
    {
        $workout = $this->getDoctrine()
            ->getRepository('AppBundle:Workout')
            ->find($id);
        if (!$workout){
            throw $this->createNotFoundException(
                'No workout found for id '.$id
            );
        }
        $data = [];
        $form = $this->createFormBuilder($data)
            ->add('rating', 'choice',
                array('choices' => array(
                    '1'   => '1',
                    '2'   => '2',
                    '3'   => '3',
                    '4'   => '4',
                    '5'   => '5',
                ), 'expanded' => true))
            ->getForm();
        $form->handleRequest($request);
        $data = $form->getData();
        if (isset($data['rating'])) {
            $workout->setUserRating($this->getUser(), $data['rating']);
            $doc = $this->getDoctrine()->getManager();
            $doc->persist($workout);
            $doc->flush();
        }
        return $this->render('@App/Home/rateWorkout.html.twig', array(
            'form' => $form->createView(), 'workout' => $workout
        ));
    }
    public function showWorkoutAction($id, Request $request)
    {
        $repo = $this->get('app.repo');
        $workout = $repo->showWorkout($id);
        if (!$workout){
            throw $this->createNotFoundException(
                'No workout found for id '.$id
            );
        }
        //Komentaru forma imest.
        $user = $this->getUser();
        $comment = new Comments($user, "");
        $form = $this->createFormBuilder($comment)
            ->add('comment', TextareaType::class)
            ->getForm();
        $form->add('parent', HiddenType::class, array (
            'data' => null
        ));
        $form->add('save', SubmitType::class, array('label' => 'Komentuoti'));
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
        $data = [];
        $formRate = $this->createFormBuilder($data)
            ->add('rating', 'choice',
                array('choices' => array(
                    '1'   => '1',
                    '2'   => '2',
                    '3'   => '3',
                    '4'   => '4',
                    '5'   => '5',
                ), 'expanded' => true))
            ->getForm();
        $formRate->handleRequest($request);
        $data = $formRate->getData();
        if (isset($data['rating'])) {
            $workout->setUserRating($this->getUser(), $data['rating']);
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
    public function showWorkoutsPageAction(Request $request)
    {
        $page = $request->query->get("page");
        if ($page==null) {
            $page = 0;
        }
        $sort = $request->query->get("sort");
        if ($sort==null) {
            $sort = "rating";
        }
        $difficulty = $request->query->get("difficulty");
        $search = $request->query->get("search");
        $type = $request->query->get("type");
        $equipment = $request->query->get("equipment");
        $muscle = $request->query->get("muscle");


        $whereState="WHERE ";
        $sortState="Workouts." . $sort;
        $start = $page*4;

        if ($difficulty!=null) {
            $whereState = $whereState . "Workouts.difficulty = :diff AND ";
        }
        if ($search!=null) {
            $whereState = $whereState . "Workouts.title LIKE :search AND ";
        }
        if ($type!=null) {
            foreach ($type as $i) {
                $whereState = $whereState . "IN(:type" . $i . ", Workouts.type) AND ";
            }
        }
        if ($equipment!=null) {
            foreach ($equipment as $i) {
                $whereState = $whereState . "FIND_IN_SET(:equipment" . $i . ", Workouts.equipment) AND ";
            }
        }
        if ($muscle!=null) {
            foreach ($muscle as $i) {
                $whereState = $whereState . "IN(:muscle" . $i . ", Workouts.muscle_group) AND ";
            }
        }

        if ($whereState=="WHERE ") {
            $whereState="";
        } else {
            $whereState=substr($whereState, 0, -5);
        }

        $query = "SELECT Workouts.id,title, Workouts.rating,description, data_created, " .
                 "Workouts.creator_id, Workouts.difficulty, username FROM Workouts " .
                 "LEFT JOIN fos_user ON fos_user.id=Workouts.creator_id " . $whereState .
                 " ORDER BY " . $sortState . " DESC LIMIT " . $start . ",4";

        $stmt = $this->getDoctrine()->getEntityManager()
            ->getConnection()
            ->prepare($query);
        if ($difficulty != null) {
            $stmt->bindValue('diff', $difficulty);
        }
        if ($search!=null) {
            $stmt->bindValue('search', "%" . $search . "%");
        }
        if ($type!=null) {
            foreach ($type as $i) {
                $stmt->bindValue('type' . $i, $i);
            }
        }
        if ($equipment!=null) {
            foreach ($equipment as $i) {
                $stmt->bindValue('equipment' . $i, $i);
            }
        }
        if ($muscle!=null) {
            foreach ($muscle as $i) {
                $stmt->bindValue('muscle' . $i, $i);
            }
        }

        $stmt->execute();

        $workouts = $stmt->fetchAll();

        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($workouts, "json");

        return new Response($json);
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
        return $this->render('@App/Home/showUser.html.twig', array(
            'id' => $id
        ));
    }
    public function activateWorkout($id, Request $request)
    {
        $workout = $this->getDoctrine()
            ->getRepository('AppBundle:Workout')
            ->find($id);
        $disabled = false;
        if ($request->getContent("Hidden")!=null) {
            $disabled=true;
        }
        if ($this->getUser()->getActiveWorkout()!=null)
            if ($this->getUser()->getActiveWorkout()->getId()==$id) {
                $disabled=true;
            }
        $form = $this->createFormBuilder()
            ->add('Hidden', HiddenType::class, array(
                'data' => '0'
            ))
            ->add('Aktyvuoti', SubmitType::class, array (
                'disabled'=>$disabled
            ))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if ($user!=null) {
                $history = new WorkoutHistory($user, $workout);
                $user->setActiveWorkout($workout);
                $user->addWorkoutHistory($history);
                $doc = $this->getDoctrine()->getManager();
                $doc->persist($history);
                $doc->persist($user);
                $doc->flush();
            }
        }
        return $form->createView();
    }
}