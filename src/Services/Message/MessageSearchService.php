<?php

namespace App\Services\Message;

use App\Entity\User;
use App\Entity\Discussion;
use App\Entity\SearchMessage;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\Pagination\Pagination;
use App\Services\User\UserService;
use App\Contracts\Message\MessageSearchServiceInterface;
use App\Contracts\Message\MessageSearchInterface;
use App\Contracts\Message\PaginationInterface;
use Elastica\Query\BoolQuery;
use Elastica\Query;
use Elastica\Query\MatchQuery;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Elastica\Query\Range;
use DateTimeImmutable;
use Elastica\Query\Wildcard;
use Elastica\Query\MatchPhrase;
use Elastica\Query\MultiMatch;
use App\Controller\Messaging\Search\Trait\BaseSearchTrait;

class MessageSearchService implements MessageSearchInterface
{
    use BaseSearchTrait;

    private const SEARCH_RESULT_LIMIT = 1000;
    private const ITEMS_PER_PAGE = 5;

    public function __construct(
        private readonly PaginatedFinderInterface $finder,
        private readonly EntityManagerInterface $em,
        private readonly UserService $userService,
        private readonly PaginationInterface $pagination
    ) {}

    /**
     * Get pagination information for messages
     *
     * @param int $idDiscussion The discussion to search in
     * @param int $page The current page number
     * @param SearchMessage|null $criteria The search criteria
     *
     * @return array The pagination information
     */
    public function messages(int $idDiscussion, int $page, ?SearchMessage $criteria): array 
    {
        $discussion = $this->em
            ->getRepository(Discussion::class)->find($idDiscussion); 

        $boolQuery = new BoolQuery();
    
        // Add discussion filter if specified
        if ($discussion) {
            $boolQuery->addFilter(new MatchQuery('discussion.id', $discussion->getId()));
        }

        // Add search criteria if specified
        if ($criteria) {
            $this->addSearchCriteria($boolQuery, $criteria);
        }

        $data = $this->executeSearch($boolQuery);

        return $this->pagination->pagination($page, self::ITEMS_PER_PAGE, $data);
    }

     /**
     * Add search criteria to the query
     * 
     * @param BoolQuery $boolQuery The boolean query to modify
     * @param SearchMessage $criteria The search criteria
     * 
     * @return void
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
     * 
     * @param BoolQuery $boolQuery The boolean query to execute
     * 
     * @return array The search results
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

        return $this->finder->find($query, self::SEARCH_RESULT_LIMIT);
    }
}