<?php

namespace App\UseCase\Autocomplete;

use App\Repository\EquipmentAssetRepository;

class FindEquipmentAssetUseCase
{
    public function __construct(private readonly EquipmentAssetRepository $equipmentRepo){}

    public function execute(string $q, int $company): array
    {
        return $this->equipmentRepo->findByLike($q, $company);
    }
}