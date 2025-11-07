<?php

namespace App\Factory;

use App\Entity\Discussion;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use App\Factory\UserFactory;

class DiscussionFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        $personInvitationSender = UserFactory::random();

        // Obtenez tous les utilisateurs
        $users = UserFactory::all();

        dump($users);

        // Filtrez l'utilisateur à exclure
        $filteredUsers = array_filter($users, fn($user) => $user->getId() !== $personInvitationSender->getId());

        // Sélectionnez un utilisateur aléatoire parmi ceux restants
        $personInvitationRecipient = $filteredUsers[array_rand($filteredUsers)];
        
        return [
            'personInvitationSender' =>$personInvitationSender,
            'personInvitationRecipient' => $personInvitationRecipient,
            'creatorUser' => $personInvitationSender,
            'dateCreation' => self::faker()->dateTime(),
            'dateModification' => self::faker()->dateTime(),
        ];
    }

    protected static function getClass(): string
    {
        return Discussion::class;
    }
}