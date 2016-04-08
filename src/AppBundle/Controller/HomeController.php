<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comments;
use AppBundle\Entity\Regime;
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

class HomeController extends Controller
{
    /**
     * Home page index action. 
     * Shows currently popular regimes
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $repo = $this->get('app.repo');
        $regimes = $repo->getHotRegimes();

        //TODO kadangi vistiek darom su angularu, tai grazinti tiesiog response, o ne render()
        return $this->render('@App/Home/indexShow.html.twig', array(
            'regimes' => $regimes
        ));
    }

    /**
     * Create a new Regime
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createRegimeAction(Request $request)
    {
        $user = $this->getUser();
        if ($user==null) {
            return new Response('Prisijunk!');
        }
        $regime = new Regime($user, new \DateTime());
        $regime->setDataUpdated($regime->getDataCreated());

        $form = $this->createFormBuilder($regime)
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
            $regime->setSchedule($schedule);

            $form->add('schedule', CollectionType::class, array(
                'entry_type' => TextareaType::class,
                'required' => false
            ));

        $form->add('save', SubmitType::class, array('label' => 'Pridėti programą'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($regime);
            $em->flush();

            return $this->redirectToRoute('app.taskSuccess');
        }

        return $this->render('@App/Home/createRegime.html.twig', array(
            'form' => $form->createView()
        ));

    }

    /**
     * Rates regime.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rateRegimeAction($id, Request $request)
    {
        $regime = $this->getDoctrine()
            ->getRepository('AppBundle:Regime')
            ->find($id);

        if (!$regime){
            throw $this->createNotFoundException(
                'No regime found for id '.$id
            );
        }
        $data = array();
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
            $regime->setUserRating($this->getUser(), $data['rating']);
            $doc = $this->getDoctrine()->getManager();
            $doc->persist($regime);
            $doc->flush();
        }
        return $this->render('@App/Home/rateRegime.html.twig', array(
            'form' => $form->createView(), 'regime' => $regime
        ));
    }

    public function showRegimeAction($id, Request $request)
    {
        $regime = $this->getDoctrine()
            ->getRepository('AppBundle:Regime')
            ->find($id);

        if (!$regime){
            throw $this->createNotFoundException(
                'No regime found for id '.$id
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
                $comment->setRegime($regime);
                $comments = $regime->getComments();
                $comments[] = $comment;
                $regime->setComments($comments);
                $em->persist($regime);
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

        $data = array();
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
            $regime->setUserRating($this->getUser(), $data['rating']);
            $doc = $this->getDoctrine()->getManager();
            $doc->persist($regime);
            $doc->flush();
        }

        return $this->render('@App/Home/queryRegime.html.twig', array(
           "regime" => $regime,
            "form" => $form->createView(),"formRate" => $formRate->createView()
        ));
    }

    /**
     * Displayed after successfully logging in, registering, creating or updating a regime
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function taskSuccessAction()
    {
        //padaryti kad po keliu sekundziu redirectintu i ka tik sukurto regime'o puslapi
        return $this->render('@App/Home/taskSuccess.html.twig', array());
    }

    /**
     * Used when searching for regimes
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function browseRegimesAction() {
        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:Regime');

        //reiks pakeisti ta findAll ir implementuoti searcho funkcijas
        $regimes = $repository->findAll();
        $json = json_encode($regimes);

        return $this->render('@App/Home/browseRegimes.html.twig', array(
            'regimes' => $json
        ));
    }

    /**
     * Responds with json of regimes
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showRegimesAction() {
        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:Regime');

        $regimes = $repository->findAll();

        $serializer = $this->get('jms_serializer');

        $json = $serializer->toArray($regimes);

        return new Response($serializer->serialize($json, 'json'));
    }

    public function showRegimesPageAction($page) {
        $start = $page*4;

        $query = "SELECT Regimes.id,title,description, data_created, Regimes.creator_id, Regimes.difficulty, username FROM Regimes 
        LEFT JOIN fos_user ON fos_user.id=Regimes.creator_id LIMIT " . $start . ",4";

        $stmt = $this->getDoctrine()->getEntityManager()
            ->getConnection()
            ->prepare($query);
        $stmt->execute();

        $regimes = $stmt->fetchAll();

        $serializer = $this->get('jms_serializer');

        $json = $serializer->toArray($regimes);

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
        return $this->render('@App/Home/showUser.html.twig', array(
            'id' => $id
        ));
    }
}
