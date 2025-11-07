<?php
namespace App\EventListener\Contracts\Activity;

use Doctrine\ORM\Event\PreUpdateEventArgs;

interface EntityActivityHandlerInterface
{
    public function supports(object $entity): bool;
    public function handlePrePersist(object $entity): void;
    public function handlePostPersist(object $entity): void;
    public function handlePostLoad(object $entity): void;
    public function handlePreUpdate(object $entity, PreUpdateEventArgs $event): void;
}