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
     */
    public function getWorkouts($page, $sort, $difficulty, $search, $type, $equipment, $muscle)
    {
        //this has yet to be changed
    }
}
