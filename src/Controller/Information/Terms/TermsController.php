<?php

namespace App\Controller\Information\Terms;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;

class TermsController extends AbstractController
{
    const FR = 'fr';

    #[Route('/terms', name: 'app_terms')]
    public function index(RequestStack $requestStack): Response
    {
        $locale = $requestStack->getCurrentRequest()->getLocale();

        if ($locale == self::FR) {
            $pagePath = "terms/fr_index.html.twig";
        } else {
            $pagePath = "terms/en_index.html.twig";
        }

        return $this->render($pagePath, [
        ]);
    }
}
