<?php

namespace App\Factory;

use App\Entity\Message;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use App\Factory\UserFactory;
use App\Factory\SharedFixtureData;

class MessageFactory extends ModelFactory
{
    protected function getDefaults(): array
    {   
        return [
            'message' => self::faker()->text(200),
            'creatorUser' => UserFactory::random(),
            'dateCreation' => new \DateTimeImmutable(),
            'dateModification' => new \DateTimeImmutable(),
        ];
    }

    protected static function getClass(): string
    {
        return Message::class;
    }
}