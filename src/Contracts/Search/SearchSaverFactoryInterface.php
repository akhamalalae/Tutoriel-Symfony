<?php

namespace App\Contracts\Search;

use App\Entity\User;

interface SearchSaverFactoryInterface
{
    public function createEmpty(): object; // new SearchMessage() / new SearchDiscussion()
    public function loadExisting(?string $id): ?object;
    public function hydrate(object $entity, User $user, array $criteria): object;
}
