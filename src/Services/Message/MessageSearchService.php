<?php

namespace App\Services\Message;

use App\Entity\Discussion;
use App\Entity\SearchMessage;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\User\UserService;
use App\Contracts\Message\MessageSearchInterface;
use Elastica\Query\BoolQuery;
use Elastica\Query;
use Elastica\Query\MatchQuery;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use App\Trait\Search\BaseSearchTrait;

class MessageSearchService implements MessageSearchInterface
{
    use BaseSearchTrait;

    private const ITEMS_PER_PAGE = 6;

    public function __construct(
        private readonly PaginatedFinderInterface $finder,
        private readonly EntityManagerInterface $em,
        private readonly UserService $userService
    ) {}

    /**
     * Get paginated messages from Elasticsearch
     */
    public function messages(int $idDiscussion, int $page, ?SearchMessage $criteria): array 
    {
        $discussion = $this->em
            ->getRepository(Discussion::class)->find($idDiscussion); 

        $boolQuery = new BoolQuery();
    
        // Filtrer par discussion
        if ($discussion) {
            $boolQuery->addFilter(new MatchQuery('discussion.id', $discussion->getId()));
        }

        // Critères supplémentaires
        if ($criteria) {
            $this->addSearchCriteria($boolQuery, $criteria);
        }

        // Crée la requête
        $query = new Query($boolQuery);
        $query->addSort(['dateCreation' => ['order' => 'DESC']]);

        // Exécute la recherche paginée
        $paginator = $this->finder->findPaginated($query);
        $paginator->setMaxPerPage(self::ITEMS_PER_PAGE);
        $paginator->setCurrentPage($page);

        // Résultats de la page courante
        $results = iterator_to_array($paginator->getCurrentPageResults());

        // Construction des métadonnées de pagination
        return [
            'items' => array_reverse($results),
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

    private function addSearchCriteria(BoolQuery $boolQuery, SearchMessage $criteria): void
    {
        $message = $criteria->getSensitiveDataMessage();
        $fileName = $criteria->getSensitiveDataFileName();
        $createdThisMonth = $criteria->isCreatedThisMonth();

        $multiFieldGroup = new BoolQuery();

        if ($message !== '') {
            $this->multiTermSearchQuery($multiFieldGroup, 'message.sensitiveDataMessage', $message);
        }

        if ($fileName !== '') {
            $this->multiTermSearchQuery($multiFieldGroup, 'message.fileMessages.sensitiveDataName', $fileName);
        }

        $boolQuery->addMust($multiFieldGroup);

        if ($createdThisMonth) {
            $this->addDateRangeFilter($boolQuery, 'dateCreation');
        }
    }
}