<?php

namespace App\Services\Search;

use App\Contracts\Search\SearchSaverFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\SearchMessage;
use App\Entity\User;

class MessageSearchFactory implements SearchSaverFactoryInterface
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    public function createEmpty(): object
    {
        return new SearchMessage();
    }

    public function loadExisting(?string $id): ?object
    {
        return $id ? $this->em->getRepository(SearchMessage::class)->find($id) : null;
    }

    public function hydrate(object $entity, User $user, array $criteria): object
    {
        $entity->setCreatorUser($user)
            ->setCreatedThisMonth(($criteria['createdThisMonth'] ?? 'false') === 'true')
            ->setDateCreation(new \DateTime())
            ->setFileName($criteria['fileName'] ?? '')
            ->setMessage($criteria['message'] ?? '')
            ->setDescription($criteria['description'] ?? '')
            ->setSensitiveDataMessage($criteria['message'] ?? '')
            ->setSensitiveDataFileName($criteria['fileName'] ?? '');

        return $entity;
    }
}
