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

/**
 * Class Repo
 * @package AppBundle\Service
 * Workout repository.
 */
class Repo
{
    /**
     * @var EntityManager
     */
    public $entityManager;

    /**
     * Gets Entity Manager.
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
     * Gets Doctrine repository.
     * @param string $repository
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepo($repository)
    {
        return $this->entityManager
            ->getRepository($repository);
    }

    /**
     * Gets workout by id
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
     * @param SearchOptions $options
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getWorkouts(SearchOptions $options)
    {
        /**
         * Sukuria SQL query.
         */
        $query = $this->createQuery($options);
        $stmt = $this->entityManager
            ->getConnection()
            ->prepare($query);

        /**
         * Subindina reiksmes is searchOptions objekto, kad neveiktu injection'ai.
         */
        if ($options->getDifficulty() != null) {
            $stmt->bindValue('diff', $options->getDifficulty());
        }
        if ($options->getSearch() != null) {
            $stmt->bindValue('search', "%".$options->getSearch()."%");
        }
        if ($options->getType() != null) {
            foreach ($options->getType() as $i) {
                $stmt->bindValue('type'.$i, $i);
            }
        }
        if ($options->getEquipment() != null) {
            foreach ($options->getEquipment() as $i) {
                $stmt->bindValue('equipment'.$i, $i);
            }
        }
        if ($options->getMuscle() != null) {
            foreach ($options->getMuscle() as $i) {
                $stmt->bindValue('muscle_group'.$i, $i);
            }
        }
        $stmt->execute();
        /**
         * Grazina gautus duomenis.
         */
        $workouts = $stmt->fetchAll();
        return $workouts;
    }

    /**
     * Grazina SQL query rasti workouts pagal SearchOptions.
     * @param SearchOptions $options
     * @return string
     */
    private function createQuery(SearchOptions $options)
    {
        $whereState = "WHERE ";
        $sortState = "Workouts.".$options->getSort();
        /**
         * Each page has 4 workouts.
         */
        $start = $options->getPage()*4;

        /**
         * I whereState sudeda where gabalus pagal search options.
         */
        $whereState = $whereState.$options->queryDifficulty();
        $whereState = $whereState.$options->querySearch();
        $whereState = $whereState.$options->queryType();
        $whereState = $whereState.$options->queryEquipment();
        $whereState = $whereState.$options->queryMuscle();


        //Jei where state lieka tuscia, tai ji pasalina, kitu atveju panaikina paskutini "AND ".
        if ($whereState == "WHERE ") {
            $whereState = "";
        } else {
            $whereState = substr($whereState, 0, -5);
        }

        /**
         * Pagrindinis query, i kuri sumeta WHERE ir ORDER BY states.
         */
        $query = "SELECT Workouts.id, title, Workouts.rating, description, data_created, ".
            "Workouts.creator_id, Workouts.difficulty, username FROM Workouts ".
            "LEFT JOIN fos_user ON fos_user.id=Workouts.creator_id ".$whereState.
            " ORDER BY ".$sortState." DESC LIMIT ".$start.",4";
        /**
         * SQL query takes Workouts and Creators information.
         */
        return $query;
    }
}
