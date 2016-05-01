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
                $whereState = $whereState . "FIND_IN_SET(:type" . $i . ", Workouts.type) AND ";
            }
        }
        if ($equipment!=null) {
            foreach ($equipment as $i) {
                $whereState = $whereState . "FIND_IN_SET(:equipment" . $i . ", Workouts.equipment) AND ";
            }
        }
        if ($muscle!=null) {
            foreach ($muscle as $i) {
                $whereState = $whereState . "FIND_IN_SET(:muscle" . $i . ", Workouts.muscle_group) AND ";
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
}
