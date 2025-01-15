<?php

namespace App\Controller\Information\Privacy;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\Breadcrumb\BreadcrumbService;
use Symfony\Component\HttpFoundation\RequestStack;

class PrivacyController extends AbstractController
{
    const FR = 'fr';

    #[Route('/privacy', name: 'privacy_policy')]
    public function index(
        BreadcrumbService $breadcrumbService,
        RequestStack $requestStack
    ): Response
    {
        $breadcrumbService->addBreadcrumb('Privacy', $this->generateUrl('privacy_policy'));

        $locale = $requestStack->getCurrentRequest()->getLocale();

        if ($locale == self::FR) {
            $pagePath = "privacy/fr_index.html.twig";
        } else {
            $pagePath = "privacy/en_index.html.twig";
        }

        return $this->render($pagePath, [
            'page_title' => 'Privacy',
        ]);
    }
}
