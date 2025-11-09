<?php

namespace App\EventListener;

use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use App\EventListener\Activity\Router\ActivityRouter;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

/**
 * Abonnement aux événements Doctrine pour router les activités.
 */
final class DatabaseActivitySubscriber implements EventSubscriberInterface
{
    public function __construct(private ActivityRouter $router) {}

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::postPersist,
            Events::postLoad,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->router->route('prePersist', $args->getObject());
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->router->route('postPersist', $args->getObject());
    }

    public function postLoad(LifecycleEventArgs $args): void
    {
        $this->router->route('postLoad', $args->getObject());
    }

    public function preUpdate(PreUpdateEventArgs $event): void
    {
        $this->router->route('preUpdate', $event->getEntity(), $event);
    }
}

