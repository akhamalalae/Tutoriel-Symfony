<?php

namespace App\EventListener\Activity\Handler;

use App\Entity\SearchMessage;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use App\EventListener\Contracts\Activity\EntityActivityHandlerInterface;
use App\EventListener\Contracts\Activity\ActivitySearchMessageInterface;

/**
 * Gestionnaire d'activité pour l'entité SearchMessage.
 */
final class SearchMessageActivityHandler implements EntityActivityHandlerInterface
{
    public function __construct(
        private ActivitySearchMessageInterface $activity,
    ) {}

    /**
     * Vérifie si l'entité est une instance de SearchMessage.
     * 
     * @param object $entity
     * 
     * @return bool
     */
    public function supports(object $entity): bool
    {
        return $entity instanceof SearchMessage;
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
        $this->activity->encryptSearchMessage($entity);
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
        $this->activity->decryptSearchMessage($entity);
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
        $this->activity->decryptSearchMessage($entity);
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
        $this->activity->encryptSearchMessage($entity);
    }
}

