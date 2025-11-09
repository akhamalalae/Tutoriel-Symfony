<?php

namespace App\EventListener\Activity\Router;

use App\Contracts\Activity\EntityActivityHandlerInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Route les actions d'activité vers les gestionnaires appropriés en fonction de l'entité.
 * 
 * La première fois qu’une entité passe par le router :
 * Symfony parcourt tous les handlers (supports()).
 * Trouve celui qui correspond. Stocke ce handler dans le cache PSR-6 (clé = classe de l’entité).
 * Ensuite :
 * À chaque appel suivant pour la même entité, le handler est lu directement depuis le cache sans refaire la boucle.
 * Résultat :
 * 0 overhead de boucle sur 10+ handlers à chaque cycle Doctrine.
 * Très haute performance, surtout si tu as des dizaines d’entités.
 */
final class ActivityRouter
{
    /** @var EntityActivityHandlerInterface[] */
    private array $handlers;

    /**
     * @param EntityActivityHandlerInterface[] $handlers
     */
    public function __construct(
        array $handlers,
        private CacheInterface $cache
    ){
        $this->handlers = $handlers;
    }

    /**
     * Route l'action vers le gestionnaire approprié en fonction de l'entité.
     * 
     * @param string $action
     * @param object $entity
     * @param PreUpdateEventArgs|null $event
     * 
     * @return void
     */
    public function route(string $action, object $entity, ?PreUpdateEventArgs $event = null): void
    {
        $handler = $this->getHandlerForEntity($entity);

        if (!$handler) {
            return; // aucun handler trouvé
        }

        match ($action) {
            'prePersist'    => $handler->handlePrePersist($entity),
            'postPersist'   => $handler->handlePostPersist($entity),
            'postLoad'      => $handler->handlePostLoad($entity),
            'preUpdate'     => $handler->handlePreUpdate($entity, $event),
            default         => null,
        };
    }

    /**
     * Récupère le gestionnaire d'activité approprié pour une entité donnée.
     * 
     * @param object $entity
     * 
     * @return EntityActivityHandlerInterface|null
     */
    private function getHandlerForEntity(object $entity): ?EntityActivityHandlerInterface
    {
        $key = 'activity_handler_' . md5(get_class($entity));

        $className = $this->cache->get($key, function (ItemInterface $item) use ($entity) {
            $item->expiresAfter(3600);

            foreach ($this->handlers as $handler) {
                if ($handler->supports($entity)) {
                    return get_class($handler); // On stocke le nom de la classe
                }
            }

            return null;
        });

        if ($className === null) {
            return null;
        }

        // Retrouve l’instance correspondante
        foreach ($this->handlers as $handler) {
            if ($handler::class === $className) {
                return $handler;
            }
        }

        return null;
    }
}

