<?php

namespace App\Controller\Pagination;

use App\Entity\User;
use App\Entity\Discussion;
use App\Entity\SearchMessage;
use App\Entity\SearchDiscussion;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\DiscussionMessageUser;
use App\Controller\Messaging\Search\Discussion\SearchDiscussions;
use App\Controller\Messaging\Search\Message\SearchMessages;

class Pagination
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SearchDiscussions $searchDiscussions,
        private readonly SearchMessages $searchMessages
    ) {}
    
    /**
     * Get pagination for discussion messages
     *
     * @param int $page The current page number
     * @param int $limit The number of items per page
     * @param Discussion $discussion The discussion to paginate
     * @param SearchMessage|null $criteria Optional search criteria
     * 
     * @return array Contains paginated data and pagination info
     */
    public function paginationMessage(int $page, int $limit, Discussion $discussion, ?SearchMessage $criteria): array
    {
        $data = $this->searchMessages->messages($discussion, $criteria);

        $totalPages = $this->totalPages(count($data), $limit);

        return [
            'data' => array_reverse(array_slice($data, $this->offset($page, $limit), $limit)),
            'limit' => $limit,
            'totalPages' => $totalPages
        ];
    }

    /**
     * Get pagination for discussion
     *
     * @param int $page The current page number
     * @param int $limit The number of items per page
     * @param SearchDiscussion|null $criteria Optional search criteria
     * 
     * @return array Contains paginated data and pagination info
     */
    public function paginationDiscussion(int $page, int $limit, ?SearchDiscussion $criteria): array
    {
        $data = $this->searchDiscussions->discussions($criteria);

        $totalPages = $this->totalPages(count($data), $limit);

        return [
            'data' => array_slice($data, $this->offset($page, $limit), $limit),
            'limit' => $limit,
            'totalPages' => $totalPages
        ];
    }

    public function offset(int $page, int $limit): int
    {
        return ($page - 1) * $limit;
    }

    public function totalPages(int $num, int $limit): int
    {
        return ceil($num / $limit);
    }
}
