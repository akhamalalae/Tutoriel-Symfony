<?php

namespace App\Services\Breadcrumb;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
class BreadcrumbService
{
    private const CACHE_KEY = 'breadcrumbs';

    private RequestStack $requestStack;
    private TranslatorInterface $translator;
    private CacheInterface $cache;

    public function __construct(
        CacheInterface $cache,
        TranslatorInterface $translator,
        RequestStack $requestStack
    ){
        $this->cache = $cache;
        $this->requestStack = $requestStack;
        $this->translator = $translator;
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

        $labelTrans = $this->translator->trans($label);

        foreach ($breadcrumbs as $key => $value) {
            $locale = $this->requestStack->getCurrentRequest()->getLocale();

            if ($value['label'] === $label || $value['label'] === $labelTrans ) {
                unset($breadcrumbs[$key]);
                break;
            }
        }

        $breadcrumbs[] = ['label' => $label, 'url' => $url];

        $cacheItems = $this->cache->getItem(self::CACHE_KEY);

        $cacheItems->set($breadcrumbs);

        $this->cache->save($cacheItems);
    }
}

