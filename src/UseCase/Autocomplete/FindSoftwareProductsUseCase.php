<?php

namespace App\UseCase\Autocomplete;

use App\Repository\SoftwareProductRepository;

class FindSoftwareProductsUseCase
{
    public function __construct(private readonly SoftwareProductRepository $repo){}

    public function execute(string $q): array
    {
        return $this->repo->findByLike($q);
    }
}