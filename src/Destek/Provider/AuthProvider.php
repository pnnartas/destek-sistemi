<?php


namespace Destek\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken as UserToken;

class AuthProvider implements AuthenticationProviderInterface
{
    private $userProvider;

    public function __construct(UserProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    public function authenticate(TokenInterface $token)
    {

        $user = $this->userProvider->loadUserByUsername($token->getUser(),$token->getCredentials());

        $newToken = new UserToken($token->getUser(), $token->getCredentials(), "main", $user->getRoles());

        $username = $newToken->getUser();
        if (empty($username)) {
            throw new BadCredentialsException('Bad credentials :)');
        }

        return $newToken;
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof UserToken;
    }
}