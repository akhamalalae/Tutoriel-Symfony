<?php

namespace App\EventListener\Activity\Handler;

use App\Entity\SearchDiscussion;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use App\EventListener\Contracts\Activity\EntityActivityHandlerInterface;
use App\EventListener\Contracts\Activity\ActivitySearchDiscussionInterface;

/**
 * Gestionnaire d'activité pour l'entité SearchDiscussion.
 */
final class SearchDiscussionActivityHandler implements EntityActivityHandlerInterface
{
    public function __construct(
        private ActivitySearchDiscussionInterface $activity,
    ) {}

    /**
     * Vérifie si l'entité est une instance de SearchDiscussion.
     * 
     * @param object $entity
     * 
     * @return bool
     */
    public function supports(object $entity): bool
    {
        return $entity instanceof SearchDiscussion;
    }

    /**
     * Gère l'encryptage avant la persistance.
     * 
     * @param object $entity
     * 
     * @return void
     */
    public function handlePrePersist(object $entity): void
    {
        $this->activity->encryptSearchDiscussion($entity);
    }

    /**
     * Gère le décryptage après la persistance.
     * 
     * @param object $entity
     * 
     * @return void
     */
    public function handlePostPersist(object $entity): void
    {
        $this->activity->decryptSearchDiscussion($entity);
    }

    /**
     * Gère le décryptage après le chargement.
     * 
     * @param object $entity
     * 
     * @return void
     */
    public function handlePostLoad(object $entity): void
    {
        $this->activity->decryptSearchDiscussion($entity);
    }

    /**
     * Gère l'encryptage avant la mise à jour.
     * 
     * @param object $entity
     * @param PreUpdateEventArgs $event
     * 
     * @return void
     */
    public function handlePreUpdate(object $entity, PreUpdateEventArgs $event): void
    {
        // Si User a besoin d'une logique spécifique pour preUpdate
        $this->activity->encryptSearchDiscussion($entity);
    }
}

