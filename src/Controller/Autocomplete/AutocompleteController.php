<?php

namespace App\Controller\Autocomplete;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\UseCase\Autocomplete\FindUsersAssignedToUseCase;
use App\UseCase\Autocomplete\FindEquipmentModelUseCase;
use App\UseCase\Autocomplete\FindEquipmentAssetUseCase;
use App\UseCase\Autocomplete\FindSoftwareProductsUseCase;

class AutocompleteController extends AbstractController
{
    public function __construct(
        private FindUsersAssignedToUseCase $usersAssigned,
        private FindEquipmentModelUseCase $equipmentMode,
        private FindEquipmentAssetUseCase $equipmentAsset,
        private FindSoftwareProductsUseCase $softwareProducts
    ) {}

    #[Route('/user/api/autocomplete', name: 'autocomplete', methods: ['GET'])]
    public function autocomplete(Request $request): JsonResponse
    {
        $type = (string) $request->query->get('type', '');
        $q = trim((string) $request->query->get('q', ''));
        $company = $request->query->getInt('company');

        if (!$type || !$q) {
            return $this->json([]);
        }

        return match ($type) {
            'user' => $this->formatUsers(
                $this->usersAssigned->execute($q)
            ),

            'equipment' => $this->formatEquipments(
                $this->equipmentMode->execute($q, $company)
            ),

            'equipmentAsset' => $this->formatEquipmentAssets(
                $this->equipmentAsset->execute($q, $company)
            ),

            'softwareProduct' => $this->formatSoftwareProducts(
                $this->softwareProducts->execute($q)
            ),

            default => $this->json([]),
        };
    }

    private function formatUsers(array $users): JsonResponse
    {
        return $this->json(array_map(fn($u) => [
            'id' => $u->getId(),
            'name' => $u->getSensitiveDataFullName(),
        ], $users));
    }

    private function formatEquipments(array $equipments): JsonResponse
    {
        return $this->json(array_map(fn($e) => [
            'id' => $e->getId(),
            'name' => $e->getName(),
        ], $equipments));
    }

    private function formatEquipmentAssets(array $equipments): JsonResponse
    {
        return $this->json(array_map(fn($e) => [
            'id' => $e->getId(),
            'name' => sprintf(
                '%s (%s) [%s - %s]',
                $e->getReference(),
                $e->getSerialNumber(),
                ucfirst(strtolower($e->getStatus()->value)),
                ucfirst(strtolower($e->getAssetCondition()->value))
            ),
        ], $equipments));
    }

    private function formatSoftwareProducts(array $products): JsonResponse
    {
        return $this->json(array_map(fn($s) => [
            'id' => $s->getId(),
            'name' => $s->getName(),
        ], $products));
    }
}