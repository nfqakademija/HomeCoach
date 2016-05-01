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
        $page = $request->query->get("page");
        if ($page==null) {
            $page = 0;
        }
        $sort = $request->query->get("sort");
        if ($sort==null || $sort=="rating") {
            $sort = "rating";
        } else {
            $sort = "data_created";
        }
        $difficulty = $request->query->get("difficulty");
        $search = $request->query->get("search");
        $type = $request->query->get("type");
        $equipment = $request->query->get("equipment");
        $muscle = $request->query->get("muscle");

        $this->get("app.repo")->entityManager = $this->getDoctrine()->getEntityManager();
        $workouts = $this->get("app.repo")->getWorkouts($page, $sort, $difficulty, $search, $type, $equipment, $muscle);
        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($workouts, "json");

        return new Response($json);
    }
}
