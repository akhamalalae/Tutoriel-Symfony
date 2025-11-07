<?php

namespace App\EventListener\Activity\Handler;

use App\Entity\User;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use App\EventListener\Contracts\Activity\EntityActivityHandlerInterface;
use App\EventListener\Contracts\Activity\ActivityUserInterface;

/**
 * Gestionnaire d'activité pour l'entité User.
 */
final class UserActivityHandler implements EntityActivityHandlerInterface
{
    public function __construct(
        private ActivityUserInterface $activity,
    ) {}

    /**
     * Vérifie si l'entité est une instance de User.
     * 
     * @param object $entity
     * 
     * @return bool
     */
    public function supports(object $entity): bool
    {
        return $entity instanceof User;
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
        $this->activity->encryptUser($entity);
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
        $this->activity->decryptUser($entity);
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
        $this->activity->decryptUser($entity);
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
        $this->activity->encryptPreUpdateUser($entity, $event);
    }
}

