<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Regime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class HomeController extends Controller
{
    /**
     * Home page index action.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('AppBundle:Home:index.html.twig', array(
            // ...
        ));
    }

    /**
     * Shows currently popular regimes
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showRegimesAction()
    {
        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:Regime');

        $regimes = $repository->findAll();

        return $this->render('AppBundle:Home:hotRegimes.html.twig', array(
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
        $regime = new Regime();
        $regime->setDataCreated(new \DateTime());
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
            ->add('description', TextType::class)
            ->getForm();
            $schedule = array (null, null, null, null, null, null, null);
            $regime->setSchedule($schedule);

            $form->add('schedule', CollectionType::class, array(
                'entry_type' => TextType::class,
                'required' => false
            ));

        $form->add('save', SubmitType::class, array('label' => 'create regime'));

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
    public function rateRegimeAction($id, $user, Request $request)
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
                ), 'expanded' => true,
                    'required' => false))
            ->add('save', SubmitType::class, array('label' => 'rate regime'))
            ->getForm();

        $form->handleRequest($request);
        $data = $form->getData();
        if (isset($data['rating'])) {
            $regime->setUserRating($user, $data['rating']);
            $doc = $this->getDoctrine()->getManager();
            $doc->persist($regime);
            $doc->flush();
        }
        return $this->render('@App/Home/rateRegime.html.twig', array(
            'form' => $form->createView(), 'regime' => $regime
        ));
    }

    public function showRegimeAction($id)
    {
        $regime = $this->getDoctrine()
            ->getRepository('AppBundle:Regime')
            ->find($id);

        if (!$regime){
            throw $this->createNotFoundException(
                'No regime found for id '.$id
            );
        }

        return $this->render('@App/Home/queryRegime.html.twig', array(
           "regime" => $regime
        ));
    }

    public function taskSuccessAction()
    {
        //padaryti kad po keliu sekundziu redirectintu i ka tik sukurto regime'o puslapi
        return $this->render('@App/Home/taskSuccess.html.twig', array());
    }

}
