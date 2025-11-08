<?php

namespace App\EventListener\Activity\Handler;

use App\Entity\FileMessage;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use App\Contracts\Activity\EntityActivityHandlerInterface;
use App\Contracts\Activity\ActivityFileMessageInterface;

/**
 * Gestionnaire d'activité pour l'entité FileMessage.
 */
final class FileMessageActivityHandler implements EntityActivityHandlerInterface
{
    public function __construct(
        private ActivityFileMessageInterface $activity,
    ) {}

    /**
     * Vérifie si l'entité est une instance de FileMessage.
     * 
     * @param object $entity
     * 
     * @return bool
     */
    public function supports(object $entity): bool
    {
        return $entity instanceof FileMessage;
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
        $this->activity->encryptFileMessage($entity);
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
        $this->activity->decryptFileMessage($entity);
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
        $this->activity->decryptFileMessage($entity);
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

