<?php
/**
 * Created by PhpStorm.
 * User: saulius.vaitkevicius
 * Date: 4/7/2016
 * Time: 11:39 AM
 */

namespace AppBundle\Service;


class Repo
{
    /**
     * Repo constructor.
     */
    public function __construct()
    {
    }
    public function showHotRegimes() {
        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:Regime');

        $regimes = $repository->findBy(array(), array('rating' => 'DESC'),5);
        $json = json_encode($regimes);

        return $this->render('@App/Home/index.html.twig', array(
            'regimes' => $regimes
        ));
    }
}