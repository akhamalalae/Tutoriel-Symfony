<?php

namespace App\Services\Search;

use App\Contracts\Search\SearchSaverFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\SearchDiscussion;
use App\Entity\User;

class DiscussionSearchFactory implements SearchSaverFactoryInterface
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    public function createEmpty(): object
    {
        return new SearchDiscussion();
    }

    public function loadExisting(?string $id): ?object
    {
        return $id ? $this->em->getRepository(SearchDiscussion::class)->find($id) : null;
    }

    public function hydrate(object $entity, User $user, array $criteria): object
    {
        $entity->setCreatorUser($user)
            ->setCreatedThisMonth(($criteria['createdThisMonth'] ?? 'false') === 'true')
            ->setDateCreation(new \DateTime())
            ->setName($criteria['name'] ?? '')
            ->setFirstName($criteria['firstName'] ?? '')
            ->setDescription($criteria['description'] ?? '')
            ->setSensitiveDataName($criteria['name'] ?? '')
            ->setSensitiveDataFirstName($criteria['firstName'] ?? '');

        return $entity;
    }
}
