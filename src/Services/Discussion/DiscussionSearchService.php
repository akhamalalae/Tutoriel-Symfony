<?php

namespace App\Services\Discussion;

use App\Services\User\UserService;
use App\Entity\User;
use App\Entity\SearchDiscussion;
use Doctrine\ORM\EntityManagerInterface;
use App\Contracts\Discussion\DiscussionSearchInterface;
use Elastica\Query\BoolQuery;
use Elastica\Query\Match;
use Elastica\Query\MatchPhrase;
use Elastica\Query\MatchQuery;
use App\Entity\Discussion;
use DateTimeImmutable;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Controller\Messaging\Search\Interface\SearchDiscussionsInterface;
use Elastica\Query\Term;
use Elastica\Query\Wildcard;
use Elastica\Query\Exists;
use Elastica\Query\Nested;
use App\Contracts\Message\PaginationInterface;
use App\Trait\Search\BaseSearchTrait;

class DiscussionSearchService implements DiscussionSearchInterface
{
    use BaseSearchTrait;

    private const SEARCH_RESULT_LIMIT = 1000;
    private const ITEMS_PER_PAGE = 5;

    public function __construct(
        private readonly PaginatedFinderInterface $finder,
        private readonly PaginationInterface $pagination,
        private readonly UserService $userService,
        private readonly EntityManagerInterface $em
    ) {}
    
    /**
     * Get pagination information for discussions
     *
     * @param int $page The current page number
     * @param SearchDiscussion|null $criteria The search criteria
     *
     * @return array The pagination information
     */
    public function discussions(int $page, SearchDiscussion|null $criteria): array
    {
        $user = $this->userService->getAuthenticatedUser();

        if (!$user) {
            return [];
        }

        $boolQuery = new BoolQuery();

        $this->addUserFilter($boolQuery, $user);

        if ($criteria) {
            $this->addSearchCriteria($boolQuery, $criteria);
        }

        $data = $this->executeSearch($boolQuery);

        return $this->pagination
            ->pagination($page, self::ITEMS_PER_PAGE, $data);
    }

    /**
     * Add user filter to the query
     * 
     * @param BoolQuery $boolQuery The boolean query to modify
     * @param User $user The user to filter discussions for
     * 
     * @return void
     */
    private function addUserFilter(BoolQuery $boolQuery, User $user): void
    {
        $boolQuery->addShould(new Term(['personInvitationSender.id' => $user->getId()]));
        $boolQuery->addShould(new Term(['personInvitationRecipient.id' => $user->getId()]));
        $boolQuery->setMinimumShouldMatch(1);
    }

    /**
     * Add search criteria to the query
     * 
     * @param BoolQuery $boolQuery The boolean query to modify
     * @param SearchDiscussion $criteria The search criteria
     * 
     * @return void
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

    /**
     * Execute the search query
     * 
     * @param BoolQuery $boolQuery The boolean query to execute
     * 
     * @return array The search results
     */
    private function executeSearch(BoolQuery $boolQuery): array
    {
        $query = new \Elastica\Query();
        
        $query->setQuery($boolQuery)
            ->addSort([
                'dateCreation' => ['order' => 'DESC']
            ]);

        return $this->finder->find($query, self::SEARCH_RESULT_LIMIT);
    }
}