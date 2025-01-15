<?php

namespace App\Factory;

use App\Entity\Message;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use App\Factory\UserFactory;

class MessageFactory extends ModelFactory
{
    protected function getDefaults(): array
    {   
        return [
            'message' => self::faker()->word,
            'creatorUser' => UserFactory::random(),
            'dateCreation' => self::faker()->dateTime(),
            'dateModification' => self::faker()->dateTime(),
        ];
    }

    protected static function getClass(): string
    {
        return Message::class;
    }
}