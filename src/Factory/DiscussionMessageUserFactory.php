<?php

namespace App\Factory;

use App\Entity\DiscussionMessageUser;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use App\Factory\UserFactory;
use App\Factory\MessageFactory;
use App\Factory\DiscussionFactory;

class DiscussionMessageUserFactory extends ModelFactory
{
    protected function getDefaults(): array
    {   
        return [
            'message' => MessageFactory::random(),
            'discussion' => DiscussionFactory::random(),
            'creatorUser' => UserFactory::random(),
            'dateCreation' => self::faker()->dateTime(),
            'dateModification' => self::faker()->dateTime(),
        ];
    }

    protected static function getClass(): string
    {
        return DiscussionMessageUser::class;
    }
}