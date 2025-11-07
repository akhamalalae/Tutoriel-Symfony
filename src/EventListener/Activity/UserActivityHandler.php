<?php

namespace App\EventListener\Activity;

use App\Entity\User;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use App\EventListener\Contracts\Activity\EntityActivityHandlerInterface;
use App\EventListener\Contracts\Activity\ActivityUserInterface;

final class UserActivityHandler implements EntityActivityHandlerInterface
{
    public function __construct(
        private ActivityUserInterface $activity,
    ) {}

    public function supports(object $entity): bool
    {
        return $entity instanceof User;
    }

    public function handlePrePersist(object $entity): void
    {
        $this->activity->encryptUser($entity);
    }

    public function handlePostPersist(object $entity): void
    {
        $this->activity->decryptUser($entity);
    }

    public function handlePostLoad(object $entity): void
    {
        $this->activity->decryptUser($entity);
    }

    public function handlePreUpdate(object $entity, PreUpdateEventArgs $event): void
    {
        // Si User a besoin d'une logique spécifique pour preUpdate
        $this->activity->encryptPreUpdateUser($entity, $event);
    }
}

