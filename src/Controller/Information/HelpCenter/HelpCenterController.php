<?php

namespace App\Controller\Information\HelpCenter;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\Breadcrumb\BreadcrumbService;
use Symfony\Component\HttpFoundation\RequestStack;

class HelpCenterController extends AbstractController
{
    const FR = 'fr';

    #[Route('/help_center', name: 'help_center')]
    public function index(
        BreadcrumbService $breadcrumbService,
        RequestStack $requestStack
    ): Response
    {
        $breadcrumbService->addBreadcrumb('Help Center', $this->generateUrl('help_center'));

        $locale = $requestStack->getCurrentRequest()->getLocale();

        if ($locale == self::FR) {
            $pagePath = "help_center/fr_index.html.twig";
        } else {
            $pagePath = "help_center/en_index.html.twig";
        }

        return $this->render($pagePath, [
            'page_title' => 'Help Center',
        ]);
    }
}