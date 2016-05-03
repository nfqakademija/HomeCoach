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


    public function getWorkouts(SearchOptions $options)
    {
        $query = $this->createQuery($options);
        $stmt = $this->entityManager
            ->getConnection()
            ->prepare($query);
        if ($options->getDifficulty()!=null) {
            $stmt->bindValue('diff', $options->getDifficulty());
        }
        if ($options->getSearch()!=null) {
            $stmt->bindValue('search', "%" . $options->getSearch() . "%");
        }
        if ($options->getType()!=null) {
            foreach ($options->getType() as $i) {
                $stmt->bindValue('type' . $i, $i);
            }
        }
        if ($options->getEquipment()!=null) {
            foreach ($options->getEquipment() as $i) {
                $stmt->bindValue('equipment' . $i, $i);
            }
        }
        if ($options->getMuscle()!=null) {
            foreach ($options->getMuscle() as $i) {
                $stmt->bindValue('muscle_group' . $i, $i);
            }
        }

        $stmt->execute();

        $workouts = $stmt->fetchAll();
        return $workouts;
    }

    private function createQuery(SearchOptions $options)
    {
        $whereState="WHERE ";
        $sortState="Workouts." . $options->getSort();
        $start = $options->getPage()*4;

        $whereState = $whereState.$options->queryDifficulty();
        $whereState = $whereState.$options->querySearch();
        $whereState = $whereState.$options->queryType();
        $whereState = $whereState.$options->queryEquipment();
        $whereState = $whereState.$options->queryMuscle();
        
        if ($whereState=="WHERE ") {
            $whereState="";
        } else {
            $whereState=substr($whereState, 0, -5);
        }

        $query = "SELECT Workouts.id,title, Workouts.rating,description, data_created, " .
            "Workouts.creator_id, Workouts.difficulty, username FROM Workouts " .
            "LEFT JOIN fos_user ON fos_user.id=Workouts.creator_id " . $whereState .
            " ORDER BY " . $sortState . " DESC LIMIT " . $start . ",4";
        return $query;
    }
}
