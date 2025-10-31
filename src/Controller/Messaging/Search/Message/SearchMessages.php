<?php

namespace App\Controller\Messaging\Search\Message;

use Elastica\Query\BoolQuery;
use Elastica\Query;
use Elastica\Query\MatchQuery;
use App\Entity\User;
use App\Entity\SearchMessage;
use App\Entity\Discussion;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Elastica\Query\Range;
use DateTimeImmutable;
use Elastica\Query\Wildcard;
use Elastica\Query\MatchPhrase;
use Elastica\Query\MultiMatch;
use App\Controller\Messaging\Search\Trait\BaseSearchTrait;

class SearchMessages extends AbstractController
{
    use BaseSearchTrait;

    private const LIMIT = 1000;

    public function __construct(
        private readonly PaginatedFinderInterface $finder,
        private readonly EntityManagerInterface $em,
    ) {}

    /**
     * Find messages based on search criteria
     *
     * @param Discussion $discussion The discussion to search in
     * @param SearchMessage|null $criteria The search criteria
     * 
     * @return array The search results
     */
    public function messages(Discussion $discussion, ?SearchMessage $criteria): array {
        
        $boolQuery = new BoolQuery();

        // Add discussion filter if specified
        if ($discussion) {
            $boolQuery->addFilter(new MatchQuery('discussion.id', $discussion->getId()));
        }

        // Add search criteria if specified
        if ($criteria) {
            $this->addSearchCriteria($boolQuery, $criteria);
        }

        return $this->executeSearch($boolQuery);
    }

    /**
     * Add search criteria to the query
     */
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

    /**
     * Execute the search query
     */
    private function executeSearch(BoolQuery $boolQuery): array
    {
        $query = new Query();
        
        // Add sorting by date in descending order
        $query->addSort([
            'dateCreation' => ['order' => 'DESC']
        ]);

        // Set the query and execute search
        $query->setQuery($boolQuery);

        return $this->finder->find($query, self::LIMIT);
    }
}
