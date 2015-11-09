<?php

namespace Destek\Controller;

use Destek\Form\LoginForm;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Destek\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;


class SecurityController
{
    public function loginAction(Request $request, Application $application)
    {
        $view = new \Zend_View();
        $form = new LoginForm();

        $form->setAttrib('class', 'form-signin');
        $form->setAction($application['url_generator']->generate('login_check'));
        $form->setView($view);

        return $application['twig']->render('security/login.html.twig', array(
            'form' => $form
        ));
    }

    public function checkAction()
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall.');
    }

    public function logoutAction()
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration');
    }
}