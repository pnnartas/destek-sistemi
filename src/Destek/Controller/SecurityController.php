<?php

namespace Destek\Controller;

use Destek\Form\LoginForm;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Destek\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Security;


class SecurityController
{
    public function loginAction(Request $request, Application $application)
    {
        $view = new \Zend_View();
        $form = new LoginForm();

        $form->setAttrib('class', 'form-signin');
        $form->setAction($application['url_generator']->generate('login_check'));
        $form->setView($view);

        $error = null;

        if (!$request->attributes->has(Security::AUTHENTICATION_ERROR)) {

            $error = $application['session']->get(Security::AUTHENTICATION_ERROR);
            $application['session']->remove(Security::AUTHENTICATION_ERROR);
        }
        if ($error !== null) {
            $error = 'Eposta ya da şifre hatalı!';
        }

        return $application['twig']->render('security/login.html.twig', array(
            'form' => $form,
            'error' => $error
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