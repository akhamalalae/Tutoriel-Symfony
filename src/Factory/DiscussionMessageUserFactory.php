<?php

namespace App\Factory;

use App\Entity\DiscussionMessageUser;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use App\Factory\UserFactory;
use App\Factory\MessageFactory;
use App\Factory\DiscussionFactory;
use App\Factory\SharedFixtureData;

class DiscussionMessageUserFactory extends ModelFactory
{
    protected function getDefaults(): array
    {   
        $date = SharedFixtureData::getNextDate();

        return [
            'message' => MessageFactory::new([
                'dateCreation' => $date,
                'dateModification' => $date,
            ]),
            'discussion' => DiscussionFactory::random(),
            'creatorUser' => UserFactory::random(),
            'dateCreation' => $date,
            'dateModification' => $date,
        ];
    }

    protected static function getClass(): string
    {
        return DiscussionMessageUser::class;
    }
}