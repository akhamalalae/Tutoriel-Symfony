<?php

namespace App\Services\Discussion;

use App\Services\User\UserService;
use App\Entity\User;
use App\Entity\SearchDiscussion;
use Doctrine\ORM\EntityManagerInterface;
use App\Contracts\Discussion\DiscussionSearchInterface;
use Elastica\Query\BoolQuery;
use Elastica\Query\Term;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use App\Trait\Search\BaseSearchTrait;

class DiscussionSearchService implements DiscussionSearchInterface
{
    use BaseSearchTrait;

    private const ITEMS_PER_PAGE = 5;

    public function __construct(
        private readonly PaginatedFinderInterface $finder,
        private readonly UserService $userService,
        private readonly EntityManagerInterface $em
    ) {}
    
    /**
     * Get paginated discussions from Elasticsearch
     *
     * @param int $page
     * @param SearchDiscussion|null $criteria
     * @return array
     */
    public function discussions(int $page, ?SearchDiscussion $criteria): array
    {
        $user = $this->userService->getAuthenticatedUser();

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

        $this->addUserFilter($boolQuery, $user);

        if ($criteria) {
            $this->addSearchCriteria($boolQuery, $criteria);
        }

        // Construction de la requête Elasticsearch
        $query = new \Elastica\Query($boolQuery);
        $query->addSort(['dateCreation' => ['order' => 'DESC']]);

        // Pagination Elasticsearch
        $paginator = $this->finder->findPaginated($query);
        $paginator->setMaxPerPage(self::ITEMS_PER_PAGE);
        $paginator->setCurrentPage($page);

        $results = iterator_to_array($paginator->getCurrentPageResults());

        // Retourne résultats + infos de pagination
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
     * Filtrer les discussions par utilisateur connecté
     */
    private function addUserFilter(BoolQuery $boolQuery, User $user): void
    {
        $boolQuery->addShould(new Term(['personInvitationSender.id' => $user->getId()]));
        $boolQuery->addShould(new Term(['personInvitationRecipient.id' => $user->getId()]));
        $boolQuery->setMinimumShouldMatch(1);
    }

    /**
     * Ajouter les critères de recherche
     */
    private function addSearchCriteria(BoolQuery $boolQuery, SearchDiscussion $criteria): void
    {
        $name = $criteria->getSensitiveDataName();
        $firstName = $criteria->getSensitiveDataFirstName();
        $createdThisMonth = $criteria->isCreatedThisMonth();

        $multiFieldGroup = new BoolQuery();

        if ($name !== '') {
            $this->multiTermSearchQuery($multiFieldGroup, 'personInvitationRecipient.sensitiveDataName', $name);
            $this->multiTermSearchQuery($multiFieldGroup, 'personInvitationSender.sensitiveDataName', $name);
        }

        if ($firstName !== '') {
            $this->multiTermSearchQuery($multiFieldGroup, 'personInvitationRecipient.sensitiveDataFirstName', $firstName);
            $this->multiTermSearchQuery($multiFieldGroup, 'personInvitationSender.sensitiveDataFirstName', $firstName);
        }

        $boolQuery->addMust($multiFieldGroup);

        if ($createdThisMonth) {
            $this->addDateRangeFilter($boolQuery, 'dateCreation');
        }
    }
}