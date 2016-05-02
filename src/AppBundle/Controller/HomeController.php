<?php

namespace AppBundle\Controller;

use Doctrine\ORM\Query;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
        return $this->render('@App/Home/index.html.twig');
    }

    public function showWorkoutsPageAction(Request $request)
    {
        $repo = $this->get('app.repo');
        $workouts = $repo->getWorkoutsPage($request);
        $serializer = $this->get('jms_serializer');
        $json_workouts = $serializer->serialize($workouts, "json");
        return new Response($json_workouts);
    }
}