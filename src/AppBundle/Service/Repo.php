<?php
/**
 * Created by PhpStorm.
 * User: saulius.vaitkevicius
 * Date: 4/7/2016
 * Time: 11:39 AM
 */

namespace AppBundle\Service;


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
    public function getHotRegimes() {
        $repository = $this->entityManager
            ->getRepository('AppBundle:Regime');
        
        $regimes = $repository->findBy(array(), array('rating' => 'DESC'),5);
        
        //TODO padaryti su JSONResponse
        $json = json_encode($regimes);

        return $json;
    }
}