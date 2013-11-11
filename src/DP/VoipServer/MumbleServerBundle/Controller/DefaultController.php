<?php

namespace DP\VoipServer\MumbleServerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('DPMumbleServerBundle:Default:index.html.twig', array('name' => $name));
    }
}
