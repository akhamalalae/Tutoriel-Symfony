<?php

namespace App\Search\Ticket;

use App\Entity\Ticket;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\MatchQuery;
use Elastica\Query\Term;
use Symfony\Component\Security\Core\Security;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use App\Trait\Search\BaseSearchTrait;

class TicketSearch
{
    use BaseSearchTrait;

    private const ITEMS_PER_PAGE = 10;

    public function __construct(
        private readonly PaginatedFinderInterface $finder,
        private readonly EntityManagerInterface $em,
        private Security $security
    ) {}

    public function search(?string $title, ?string $description, ?string $status, int $page): array
    {
        $user = $this->security->getUser();

        if (!$user) {
            return [
                'items' => [],
                'pagination' => [
                    'currentPage' => 1,
                    'perPage' => self::ITEMS_PER_PAGE,
                    'totalResults' => 0,
                    'totalPages' => 0,
                    'hasNextPage' => false,
                    'hasPreviousPage' => false,
                ],
            ];
        }

        $boolQuery = new BoolQuery();

        if (!$this->security->isGranted('ROLE_ADMIN') && !$this->security->isGranted('ROLE_MANAGER')) {
            $this->addUserFilter($boolQuery, $user);
        }

        if ($title) {
            $this->multiTermSearchQuery($boolQuery, 'title', $title);
        }

        if ($description) {
            $this->multiTermSearchQuery($boolQuery, 'description', $description);
        }

        if ($status) {
            $boolQuery->addFilter(
                new \Elastica\Query\Term(['status' => $status])
            );
        }

        $query = new Query($boolQuery);
        $query->addSort(['createdAt' => ['order' => 'DESC']]);

        $paginator = $this->finder->findPaginated($query);
        $paginator->setMaxPerPage(self::ITEMS_PER_PAGE);
        $paginator->setCurrentPage($page);

        $results = iterator_to_array($paginator->getCurrentPageResults());

        return [
            'items' => $results,
            'pagination' => [
                'currentPage' => $page,
                'perPage' => self::ITEMS_PER_PAGE,
                'totalResults' => $paginator->getNbResults(),
                'totalPages' => $paginator->getNbPages(),
                'hasNextPage' => $page < $paginator->getNbPages(),
                'hasPreviousPage' => $page > 1,
            ],
        ];
    }

    /**
     * Filtrer par utilisateur connecté
     */
    private function addUserFilter(BoolQuery $boolQuery, User $user): void
    {
        $boolQuery->addFilter(
            new Term(['createdBy.id' => $user->getId()])
        );
    }
}