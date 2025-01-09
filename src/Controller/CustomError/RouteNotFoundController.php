<?php

namespace App\Controller\CustomError;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RouteNotFoundController extends AbstractController
{
    #[Route('/custom-404', name: 'app_custom_404')]
    public function custom404(): Response
    {
        return $this->render('errors/404.html.twig', []);
    }
}
