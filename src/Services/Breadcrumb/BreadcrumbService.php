<?php

namespace App\Services\Breadcrumb;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class BreadcrumbService
{
    private CacheInterface $cache;
    
    private const CACHE_KEY = 'breadcrumbs';

    private RequestStack $requestStack;

    public function __construct(CacheInterface $cache, RequestStack $requestStack)
    {
        $this->cache = $cache;
        $this->requestStack = $requestStack;
    }

    /**
     * Récupère les breadcrumbs depuis le cache.
     */
    public function getBreadcrumbs(): array
    {
        return $this->cache->get(self::CACHE_KEY, function (ItemInterface $item) {
            $item->expiresAfter(3600); // Cache pendant 1 heure
            return [];
        });
    }

    /**
     * Met à jour les breadcrumbs avec une nouvelle page.
     */
    public function addBreadcrumb(string $label, string $url): void
    {
        $breadcrumbs = $this->getBreadcrumbs();

        // Vérifie si l'URL existe déjà pour éviter les doublons
        foreach ($breadcrumbs as $breadcrumb) {
            if ($breadcrumb['url'] === $url) {
                return; // Ne rien faire si la page existe déjà
            }
        }

        // Ajoute la nouvelle page à la liste des breadcrumbs
        //$locale = $this->requestStack->getCurrentRequest()->getLocale();
        $breadcrumbs[] = ['label' => $label, 'url' => $url];
    
        // Met à jour le cache
        $valueCacheBreadcrumbs = $this->cache->get(self::CACHE_KEY, function (ItemInterface $item) {});

        $cacheItems = $this->cache->getItem(self::CACHE_KEY);

        $cacheItems->set($breadcrumbs);

        $this->cache->save($cacheItems);
    }
}

