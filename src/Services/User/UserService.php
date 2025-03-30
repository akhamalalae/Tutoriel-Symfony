<?php

namespace App\Services\User;

use Symfony\Component\Security\Core\Security;
use App\Entity\User;

class UserService
{
    public function __construct(private Security $security) {}

    public function getAuthenticatedUser(): User
    {
        $user = $this->security->getUser();
        
        if (!$user instanceof User) {
            throw new \LogicException('No authenticated user found.');
        }

        return $user;
    }
}