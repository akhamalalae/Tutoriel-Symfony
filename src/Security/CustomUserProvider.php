<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use App\EventListener\Contracts\EncryptDecrypt\EncryptDecryptInterface;

class CustomUserProvider implements UserProviderInterface
{
    public function __construct(private EntityManagerInterface $entityManager, private EncryptDecryptInterface $encryptDecrypt)
    {
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $identifierEncrypt = $this->encryptDecrypt->encrypt($identifier);

        $user = $this->entityManager->getRepository(User::class)
            ->findOneBy(['email' => strtolower($identifierEncrypt)]);

        if (!$user) {
            throw new UserNotFoundException(sprintf('Email "%s" not found.', $identifier));
        }

        return $user;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        return $this->loadUserByIdentifier($user->getSensitiveDataEmail());
    }

    public function supportsClass(string $class): bool
    {
        return User::class === $class;
    }
}
