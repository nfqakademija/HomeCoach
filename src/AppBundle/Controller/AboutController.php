<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AboutController extends Controller
{
    public function aboutAction()
    {
        return $this->render('About/about.html.twig', array(
            // ...
        ));
    }

}
