<?php

namespace App\Controller\Home;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\Breadcrumb\BreadcrumbService;
use App\Repository\TicketRepository;
use App\Repository\EquipmentAssetRepository;

class HomeController extends AbstractController
{
    #[Route('/user', name: 'app_home')]
    public function index(
        BreadcrumbService $breadcrumbService,
        TicketRepository $ticketRepository,
        EquipmentAssetRepository $equipmentAssetRepository
    ): Response
    {
        $breadcrumbService->addBreadcrumb('Home', $this->generateUrl('app_home'));

        $ticketsStats = $ticketRepository->getDashboardTicketsStats();

        $evolutions = $this->calculateEvolution($ticketsStats);

        $stockStats = $equipmentAssetRepository->getDashboardStats();

        return $this->render('home/index.html.twig', [
            'ticketsStats' => $ticketsStats,
            'ticketsEvolution' => $evolutions,
            'stockStats' => $stockStats
        ]);
    }

    private function calculateEvolution(array $stats): array
    {
        $evolutions = [];

        foreach ($stats as $key => $value) {

            // On traite uniquement les champs this_week
            if (!str_ends_with($key, '_this_week')) {
                continue;
            }

            $baseKey = str_replace('_this_week', '', $key);
            $lastWeekKey = $baseKey . '_last_week';

            $thisWeek = (int) $value;
            $lastWeek = (int) ($stats[$lastWeekKey] ?? 0);

            // ========================
            // CALCUL %
            // ========================
            if ($lastWeek > 0) {
                $percentage = (($thisWeek - $lastWeek) / $lastWeek) * 100;
            } elseif ($thisWeek > 0) {
                $percentage = 100; // croissance infinie → on met 100%
            } else {
                $percentage = 0;
            }

            $evolutions[$baseKey] = round($percentage, 2);
        }

        return $evolutions;
    }
}
