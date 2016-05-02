<?php

namespace AppBundle\Controller;

use Doctrine\ORM\Query;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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

    public function showWorkoutsPageAction($page, $sort, $difficulty)
    {
        //TODO: Sumerginus su masteriu imest searcha i Repo
        $start = $page*4;
        if ($difficulty == 'all') {
            $whereState = "";
        } else {
            $whereState = "WHERE Workouts.difficulty= :diff";
        }

        if ($sort=="rating") {
            $query = "SELECT Workouts.id,title, Workouts.rating,description, data_created, 
Workouts.creator_id, Workouts.difficulty, username FROM Workouts 
            LEFT JOIN fos_user ON fos_user.id=Workouts.creator_id " . $whereState . " 
            ORDER BY Workouts.rating DESC LIMIT " . $start . ",4";
        } else {
            $query = "SELECT Workouts.id,title, Workouts.rating,description, data_created, 
Workouts.creator_id, Workouts.difficulty, username FROM Workouts 
            LEFT JOIN fos_user ON fos_user.id=Workouts.creator_id " . $whereState . " 
            ORDER BY Workouts.data_created DESC LIMIT " . $start . ",4";
        }

        $stmt = $this->getDoctrine()->getEntityManager()
            ->getConnection()
            ->prepare($query);
        $stmt->bindValue('diff', $difficulty);

        $stmt->execute();
        $workouts = $stmt->fetchAll();

        $serializer = $this->get('jms_serializer');

        $json = $serializer->toArray($workouts);

        return new Response($serializer->serialize($json, 'json'));
    }
}