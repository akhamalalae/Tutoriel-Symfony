<?php

namespace App\Controller\Pagination;

class Pagination
{
    /**
     * Pagination
     *
     * @param array $data
     * @param int $page
     * @param int $limit
     * 
     * @return array
     */
    public function getPagination(array $data, int $page, int $limit) : array
    {
        return [
            'data' => array_reverse(array_slice(
                    $data, 
                    $this->firstElementPagination($page, $limit), 
                    $limit
                )),
            'limit' => $limit,
            'numbrePagesPagination' => $this->numbrePagesPagination($data, $limit)
        ];
    }

    public function firstElementPagination(int $page, int $limit) : int
    {
        return ($page * $limit) - $limit;
    }

    public function numbrePagesPagination(array $data, int $limit) : int
    {
        return ceil(count($data) / $limit);
    }
}
