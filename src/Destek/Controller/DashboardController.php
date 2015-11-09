<?php

namespace Destek\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DashboardController
 * @package Destek\Controller
 */
class DashboardController
{
    /**
     * @param Application $application
     * @return mixed
     */
    public function dashboardAction(Application $application)
    {
       // var_dump($application['orm.em']->getRepository('Destek\Entity\User')->find(1));
       // exit;
        return $application['twig']->render('dashboard.html.twig');
    }
}