<?php

namespace App\Controller\Home;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\Breadcrumb\BreadcrumbService;
class HomeController extends AbstractController
{
    #[Route('/user', name: 'app_home')]
    public function index(BreadcrumbService $breadcrumbService): Response
    {
        $breadcrumbService->addBreadcrumb('Home', $this->generateUrl('app_home'));

        return $this->render('home/index.html.twig');
    }
}
