<?php

namespace App\Controller\Messaging\Search\Discussion;

use Elastica\Query\BoolQuery;
use Elastica\Query\Match;
use App\Entity\User;
use App\Entity\SearchDiscussion;
use Elastica\Query\MatchPhrase;
use Elastica\Query\MatchQuery;
use App\Entity\Discussion;
use Doctrine\ORM\EntityManagerInterface;
use DateTimeImmutable;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Controller\Messaging\Search\Interface\SearchDiscussionsInterface;
use Elastica\Query\Term;
use Elastica\Query\Wildcard;
use Elastica\Query\Exists;
use Elastica\Query\Nested;
use App\Controller\Messaging\Search\Trait\BaseSearchTrait;
use App\Services\User\UserService;

class SearchDiscussions extends AbstractController
{
    use BaseSearchTrait;

    private const LIMIT = 1000;

    public function __construct(
        private readonly PaginatedFinderInterface $finder,
        private readonly UserService $userService,
        private readonly EntityManagerInterface $em,
    ) {}

    /**
     * Find discussions based on search criteria
     * 
     * @param SearchDiscussion|null $criteria The search criteria
     * 
     * @return array The search results
     */
    public function discussions(?SearchDiscussion $criteria): array {

        $user = $this->userService->getAuthenticatedUser();

        if (!$user) {
            return [];
        }

        $boolQuery = new BoolQuery();

        $this->addUserFilter($boolQuery, $user);

        if ($criteria) {
            $this->addSearchCriteria($boolQuery, $criteria);
        }

        return $this->executeSearch($boolQuery);
    }

    /**
     * Add user filter to the query
     */
    private function addUserFilter(BoolQuery $boolQuery, User $user): void
    {
        $boolQuery->addShould(new Term(['personInvitationSender.id' => $user->getId()]));
        $boolQuery->addShould(new Term(['personInvitationRecipient.id' => $user->getId()]));
        $boolQuery->setMinimumShouldMatch(1);
    }

    /**
     * Add search criteria to the query
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
     */
    private function executeSearch(BoolQuery $boolQuery): array
    {
        $query = new \Elastica\Query();
        
        $query->setQuery($boolQuery)
            ->addSort([
                'dateCreation' => ['order' => 'DESC']
            ]);

        return $this->finder->find($query, self::LIMIT);
    }
}
