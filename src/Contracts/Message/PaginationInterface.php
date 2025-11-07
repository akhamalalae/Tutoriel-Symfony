<?php
namespace App\Contracts\Message;

interface PaginationInterface
{
    public function pagination(int $page, int $limit, array $data): array;
}
