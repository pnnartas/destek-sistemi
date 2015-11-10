<?php

namespace Destek\Handler;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class AuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler
{

    private $application;
    private $rememberMeServices;

    public function __construct(HttpUtils $httpUtils, array $options, Application $application)
    {
        parent::__construct($httpUtils, $options);
        $this->app = $application;
    }

    /**
     * Sets the RememberMeServices implementation to use
     * @param RememberMeServicesInterface $rememberMeServices
     */
    public function setRememberMeServices(RememberMeServicesInterface $rememberMeServices)
    {
        $this->rememberMeServices = $rememberMeServices;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $this->app['security.token_storage']->setToken($token);

        $session = $request->getSession();

        $stmt = $this->app['db']->executeQuery('SELECT * FROM users WHERE email = ? AND deleted = 0', array(strtolower($token->getUser())));
        $user = $stmt->fetch();

        if ($user) {
            $session->set('userId', $user['id']);
            $session->set('name', $user['name']);
            $session->set('surname', $user['surname']);
        }

        if (null !== $this->app['dispatcher']) {
            $loginEvent = new InteractiveLoginEvent($request, $token);
            $this->app['dispatcher']->dispatch(SecurityEvents::INTERACTIVE_LOGIN, $loginEvent);
        }

        $response = parent::onAuthenticationSuccess($request, $token);

        if (!$response instanceof Response) {
            throw new \RuntimeException('Authentication Success Handler did not return a Response.');
        }

        if (null !== $this->rememberMeServices) {
            $this->rememberMeServices->loginSuccess($request, $response, $token);
        }

        return $response;
    }

}