<?php

namespace App\EventListener\Activity;

use App\Entity\Message;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use App\EventListener\Contracts\Activity\EntityActivityHandlerInterface;
use App\EventListener\Contracts\Activity\ActivityMessageInterface;

final class MessageActivityHandler implements EntityActivityHandlerInterface
{
    public function __construct(
        private ActivityMessageInterface $activity,
    ) {}

    public function supports(object $entity): bool
    {
        return $entity instanceof Message;
    }

    public function handlePrePersist(object $entity): void
    {
        $this->activity->encryptMessage($entity);
    }

    public function handlePostPersist(object $entity): void
    {
        $this->activity->decryptMessage($entity);
    }

    public function handlePostLoad(object $entity): void
    {
        $this->activity->decryptMessage($entity);
    }

    public function handlePreUpdate(object $entity, PreUpdateEventArgs $event): void
    {
        // Si Message a besoin d'une logique spécifique pour preUpdate
    }
}

