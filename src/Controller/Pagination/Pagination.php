<?php

namespace App\Controller\Pagination;

use Doctrine\ORM\EntityManagerInterface;
use App\Contracts\Message\PaginationInterface;

class Pagination implements PaginationInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {}
    
    /**
     * Get pagination information
     *
     * @param int $page The current page number
     * @param int $limit The number of items per page
     * @param array $data The full dataset to paginate
     *
     * @return array The pagination information
     */
    public function pagination(int $page, int $limit, array $data): array
    {
        $totalPages = $this->totalPages(count($data), $limit);

        return [
            'data' => array_reverse(array_slice($data, $this->offset($page, $limit), $limit)),
            'limit' => $limit,
            'totalPages' => $totalPages
        ];
    }

    /**
     * Calculate the offset for pagination
     * 
     * @param int $page The current page number
     * @param int $limit The number of items per page 
     * 
     * @return int The calculated offset
     */
    public function offset(int $page, int $limit): int
    {
        return ($page - 1) * $limit;
    }

    /**
     * Calculate the total number of pages
     * 
     * @param int $num The total number of items
     * @param int $limit The number of items per page
     * 
     * @return int The total number of pages
     */
    public function totalPages(int $num, int $limit): int
    {
        return ceil($num / $limit);
    }
}
