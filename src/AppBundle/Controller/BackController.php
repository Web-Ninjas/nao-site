<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BackController extends Controller
{
    /**
     * @Route("/dashboard", name="dashboard")
     * @Method({"GET","POST"})
     */
    public function dashboardAction()
    {
        return $this->render('back/dashboard.html.twig');
    }
}