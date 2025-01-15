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
        $personInvitationRecipient = UserFactory::random();
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