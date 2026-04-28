<?php

namespace App\UseCase\Autocomplete;

use App\Repository\EquipmentModelsRepository;

class FindEquipmentModelUseCase
{
    public function __construct(private readonly EquipmentModelsRepository $equipmentRepo){}

    public function execute(string $q, int $company): array
    {
        return $this->equipmentRepo->findByNameLike($q, $company);
    }
}