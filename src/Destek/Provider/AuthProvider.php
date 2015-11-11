<?php


namespace Destek\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken as UserToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;

class AuthProvider implements AuthenticationProviderInterface
{
    private $userProvider;
    private $encoder;

    public function __construct(UserProviderInterface $userProvider, EncoderFactory $encoder)
    {
        $this->userProvider = $userProvider;
        $this->encoder = $encoder;
    }

    public function authenticate(TokenInterface $token)
    {

        $user = $this->userProvider->loadUserByUsername($token->getUser(),$token->getCredentials());

        $newToken = new UserToken($token->getUser(), $token->getCredentials(), "main", $user->getRoles());

        $encoder = $this->encoder->getEncoder($user);

        // compute the encoded password
        $encodedPassword = $encoder->encodePassword($token->getCredentials(), $user->salt);

        $newToken = new UserToken($token->getUser(), $token->getCredentials(), "secured_area", $user->getRoles());

        $username = $newToken->getUser();
        if (empty($username) || $user->getPassword() != $encodedPassword) {
            throw new BadCredentialsException('Bad credentials :)');
        }


        return $newToken;
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof UserToken;
    }
}