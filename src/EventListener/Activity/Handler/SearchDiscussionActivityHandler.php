<?php

namespace App\EventListener\Activity\Handler;

use App\Entity\SearchDiscussion;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use App\EventListener\Contracts\Activity\EntityActivityHandlerInterface;
use App\EventListener\Contracts\Activity\ActivitySearchDiscussionInterface;

final class SearchDiscussionActivityHandler implements EntityActivityHandlerInterface
{
    public function __construct(
        private ActivitySearchDiscussionInterface $activity,
    ) {}

    public function supports(object $entity): bool
    {
        return $entity instanceof SearchDiscussion;
    }

    public function handlePrePersist(object $entity): void
    {
        $this->activity->encryptSearchDiscussion($entity);
    }

    public function handlePostPersist(object $entity): void
    {
        $this->activity->decryptSearchDiscussion($entity);
    }

    public function handlePostLoad(object $entity): void
    {
        $this->activity->decryptSearchDiscussion($entity);
    }

    public function handlePreUpdate(object $entity, PreUpdateEventArgs $event): void
    {
        // Si User a besoin d'une logique spécifique pour preUpdate
        $this->activity->encryptSearchDiscussion($entity);
    }
}

