<?php

namespace App\Services\Search;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Contracts\Search\SearchSaverFactoryInterface;

abstract class AbstractSearchSaver
{
    public function __construct(
        protected readonly EntityManagerInterface $em,
        protected readonly SearchSaverFactoryInterface $factory,
    ) {}

    public function save(User $user, ?array $criteria): ?object
    {
        dump($criteria);
        if (empty($criteria)) {
            return null;
        }

        $this->validateCriteria($criteria);

        $saveSearch = $criteria['saveSearch'] === 'true' ? true : false;
        $description = $criteria['description'] ?? '';

        $existing = $this->factory->loadExisting($criteria['id'] ?? null);

        $entity = $existing ?: $this->factory->createEmpty();

        $this->factory->hydrate($entity, $user, $criteria);

        if (!$existing && $description !== '' && $saveSearch == true) {
            $this->em->persist($entity);
        }

        if ($description !== '' && $saveSearch == true) {
            $this->em->flush();
        }

        return $entity;
    }

    abstract protected function validateCriteria(array $criteria): void;
}
