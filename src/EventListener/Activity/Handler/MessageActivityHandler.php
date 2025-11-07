<?php

namespace App\EventListener\Activity\Handler;

use App\Entity\Message;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use App\EventListener\Contracts\Activity\EntityActivityHandlerInterface;
use App\EventListener\Contracts\Activity\ActivityMessageInterface;

/**
 * Gestionnaire d'activité pour l'entité Message.
 */
final class MessageActivityHandler implements EntityActivityHandlerInterface
{
    public function __construct(
        private ActivityMessageInterface $activity,
    ) {}

    /**
     * Vérifie si l'entité est une instance de Message.
     * 
     * @param object $entity
     * 
     * @return bool
     */
    public function supports(object $entity): bool
    {
        return $entity instanceof Message;
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
        $this->activity->encryptMessage($entity);
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
        $this->activity->decryptMessage($entity);
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
        $this->activity->decryptMessage($entity);
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
        // Si Message a besoin d'une logique spécifique pour preUpdate
    }
}

