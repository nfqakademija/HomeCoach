<?php
/**
 * Created by PhpStorm.
 * User: darius0021
 * Date: 16.3.24
 * Time: 13.41
 */

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends Controller
{
    public function showCommentsAction($id) {
        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:Regime');

        $regime = $repository->find($id);

        $serializer = $this->get('jms_serializer');

        $json = $serializer->serialize($regime->getComments(), 'json');

        return new Response($json);
    }
}