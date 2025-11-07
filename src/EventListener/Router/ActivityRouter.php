<?php

namespace App\EventListener\Router;

use App\EventListener\Contracts\Activity\EntityActivityHandlerInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;

final class ActivityRouter
{
    /** @var EntityActivityHandlerInterface[] */
    private array $handlers;

    /**
     * @param EntityActivityHandlerInterface[] $handlers
     */
    public function __construct(array $handlers)
    {
        $this->handlers = $handlers;
    }

    public function route(string $action, object $entity, ?PreUpdateEventArgs $event = null): void
    {
        foreach ($this->handlers as $handler) {
            if ($handler->supports($entity)) {
                match($action) {
                    'prePersist' => $handler->handlePrePersist($entity),
                    'postPersist' => $handler->handlePostPersist($entity),
                    'postLoad' => $handler->handlePostLoad($entity),
                    'preUpdate' => $handler->handlePreUpdate($entity, $event),
                    default => null,
                };
                break;
            }
        }
    }
}

