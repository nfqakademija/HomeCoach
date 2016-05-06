<?php
/**
 * Created by PhpStorm.
 * User: saulius.vaitkevicius
 * Date: 4/7/2016
 * Time: 11:39 AM
 */

namespace AppBundle\Service;

use AppBundle\Entity\Workout;
use Doctrine\ORM\EntityManager;

class Repo
{
    /**
     * @var EntityManager
     */
    public $entityManager;

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }
    
    /**
     * Repo constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $repository
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepo($repository)
    {
        
        $repo = $this->entityManager
            ->getRepository($repository);
        
        return $repo;
    }

    /**
     * @param $id
     * @return null|Workout
     */
    public function getWorkout($id)
    {
        
        $repo = $this->getRepo('AppBundle:Workout')
            ->find($id);

        return $repo;
    }

    /**
     * @return array
     */
    public function getHotWorkouts()
    {
        
        $repo = $this->getRepo('AppBundle:Workout');
        $workouts = $repo->findBy(array(), array('rating' => 'DESC'), 5);
        
        return $workouts;
    }


    /**
     * @param $page
     * @param $sort
     * @param $difficulty
     * @param $search
     * @param $type
     * @param $equipment
     * @param $muscle
     * @return mixed
     */
    public function getWorkouts($page, $sort, $difficulty, $search, $type, $equipment, $muscle)
    {
        $whereState="WHERE ";
        $sortState="Workouts." . $sort;
        $start = $page*4;

        if ($search!=null) {
            $whereState = $whereState . "Workouts.title LIKE :search AND ";
        }

        if ($difficulty!=null) {
            foreach ($difficulty as $i) {
                $whereState = $whereState . "FIND_IN_SET(:difficulty" . $i . ", Workouts.difficulty) AND ";
            }
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

        $stmt = $this->entityManager
            ->getConnection()
            ->prepare($query);

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

        if ($difficulty!=null) {
            foreach ($difficulty as $i) {
                $stmt->bindValue('difficulty' . $i, $i);
            }
        }

        if ($muscle!=null) {
            foreach ($muscle as $i) {
                $stmt->bindValue('muscle' . $i, $i);
            }
        }

        $stmt->execute();

        $workouts = $stmt->fetchAll();
        return $workouts;
    }
}
