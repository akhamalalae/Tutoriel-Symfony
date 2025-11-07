<?php

namespace App\EventListener\Activity;

use App\Entity\FileMessage;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use App\EventListener\Contracts\Activity\EntityActivityHandlerInterface;
use App\EventListener\Contracts\Activity\ActivityFileMessageInterface;

final class FileMessageActivityHandler implements EntityActivityHandlerInterface
{
    public function __construct(
        private ActivityFileMessageInterface $activity,
    ) {}

    public function supports(object $entity): bool
    {
        return $entity instanceof FileMessage;
    }

    public function handlePrePersist(object $entity): void
    {
        $this->activity->encryptFileMessage($entity);
    }

    public function handlePostPersist(object $entity): void
    {
        $this->activity->decryptFileMessage($entity);
    }

    public function handlePostLoad(object $entity): void
    {
        $this->activity->decryptFileMessage($entity);
    }

    public function handlePreUpdate(object $entity, PreUpdateEventArgs $event): void
    {
        // Si Message a besoin d'une logique spécifique pour preUpdate
    }
}

