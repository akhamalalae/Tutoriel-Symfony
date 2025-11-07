<?php

namespace App\EventListener\Activity;

use App\Entity\SearchMessage;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use App\EventListener\Contracts\Activity\EntityActivityHandlerInterface;
use App\EventListener\Contracts\Activity\ActivitySearchMessageInterface;

final class SearchMessageActivityHandler implements EntityActivityHandlerInterface
{
    public function __construct(
        private ActivitySearchMessageInterface $activity,
    ) {}

    public function supports(object $entity): bool
    {
        return $entity instanceof SearchMessage;
    }

    public function handlePrePersist(object $entity): void
    {
        $this->activity->encryptSearchMessage($entity);
    }

    public function handlePostPersist(object $entity): void
    {
        $this->activity->decryptSearchMessage($entity);
    }

    public function handlePostLoad(object $entity): void
    {
        $this->activity->decryptSearchMessage($entity);
    }

    public function handlePreUpdate(object $entity, PreUpdateEventArgs $event): void
    {
        // Si User a besoin d'une logique spécifique pour preUpdate
        $this->activity->encryptSearchMessage($entity);
    }
}

