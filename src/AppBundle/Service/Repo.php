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
    public $entityManager;
    /**
     * Repo constructor.
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    public function getRepo($repository){
        
        $repo = $this->entityManager
            ->getRepository($repository);
        
        return $repo;
    }

    public function getWorkout($id) {
        
        $repo = $this->getRepo('AppBundle:Workout')
            ->find($id);

        return $repo;
    }

    public function getHotWorkouts() {
        
        $repo = $this->getRepo('AppBundle:Workout');
        $workouts = $repo->findBy(array(), array('rating' => 'DESC'),5);
        
        return $workouts;
    }
    
    public function getWorkouts($page, $sort, $difficulty) {
        //this has yet to be changed
    }
}