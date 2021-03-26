<?php

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator;
use Symfony\Component\Security\Core\User\UserInterface;

class TokenAuthenticator extends JWTTokenAuthenticator
{
    public function checkCredentials($credentials, UserInterface $user)
    {
        return $user->getIsActive();
    }
}
