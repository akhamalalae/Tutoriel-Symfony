<?php

namespace App\Twig;

use App\Services\Breadcrumb\BreadcrumbService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BreadcrumbExtension extends AbstractExtension
{
    public function __construct(private BreadcrumbService $breadcrumbService)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('breadcrumbs', [$this, 'getBreadcrumbs']),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return $this->breadcrumbService->getBreadcrumbs();
    }
}
