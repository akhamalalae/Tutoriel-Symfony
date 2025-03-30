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
use Elastica\Query\MatchPhrase;
use Elastica\Query\Wildcard;

class SearchMessages extends AbstractController
{
    private const LIMIT = 1000;
    private const BOOST_EXACT = 1.0;
    private const BOOST_PARTIAL = 0.8;
    private const BOOST_PREFIX = 0.9;

    public function __construct(
        private readonly PaginatedFinderInterface $finder,
        private readonly EntityManagerInterface $em,
    ) {}

    /**
     * Find messages based on search criteria
     *
     * @param User $user The user performing the search
     * @param Discussion $discussion The discussion to search in
     * @param SearchMessage|null $criteria The search criteria
     * @param bool $saveSearch Whether to save the search criteria
     * @return array The search results
     */
    public function findMessages(
        User $user,
        Discussion $discussion,
        ?SearchMessage $criteria,
        bool $saveSearch
    ): array {
        $boolQuery = new BoolQuery();

        // Add discussion filter if specified
        if ($discussion) {
            $boolQuery->addFilter(new MatchQuery('discussion.id', $discussion->getId()));
        }

        // Add search criteria if specified
        if ($criteria) {
            $this->addSearchCriteria($boolQuery, $criteria);

            // Remove criteria if not needed
            if (!$saveSearch) {
                $this->removeSearchCriteria($criteria);
            }
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

        // Add message search if specified
        if ($message !== '') {
            $this->addMessageSearch($boolQuery, $message);
        }

        // Add filename search if specified
        if ($fileName !== '') {
            $this->addFileNameSearch($boolQuery, $fileName);
        }

        // Add date range filter if specified
        if ($createdThisMonth) {
            $this->addDateRangeFilter($boolQuery);
        }
    }

    /**
     * Add message search criteria
     */
    private function addMessageSearch(BoolQuery $boolQuery, string $message): void
    {
        $group1 = new BoolQuery();
        
        // Add exact match with highest boost
        $group1->addShould(
            (new MatchPhrase('message.sensitiveDataMessage', $message))
                ->setBoost(self::BOOST_EXACT)
        );

        // Add partial matches with different boosts
        $group1->addShould(
            (new Wildcard('message.sensitiveDataMessage', '*' . $message . '*'))
                ->setBoost(self::BOOST_PARTIAL)
        );
        
        $group1->addShould(
            (new Wildcard('message.sensitiveDataMessage', $message . '*'))
                ->setBoost(self::BOOST_PREFIX)
        );
        
        $group1->addShould(
            (new Wildcard('message.sensitiveDataMessage', '*' . $message))
                ->setBoost(self::BOOST_PARTIAL)
        );

        // Set minimum should match to ensure at least one condition is met
        $group1->setMinimumShouldMatch(1);

        $boolQuery->addMust($group1);
    }

    /**
     * Add filename search criteria
     */
    private function addFileNameSearch(BoolQuery $boolQuery, string $fileName): void
    {
        $group2 = new BoolQuery();
        
        // Add exact match with highest boost
        $group2->addShould(
            (new MatchPhrase('message.fileMessages.sensitiveDataName', $fileName))
                ->setBoost(self::BOOST_EXACT)
        );

        // Add partial matches with different boosts
        $group2->addShould(
            (new Wildcard('message.fileMessages.sensitiveDataName', '*' . $fileName . '*'))
                ->setBoost(self::BOOST_PARTIAL)
        );
        
        $group2->addShould(
            (new Wildcard('message.fileMessages.sensitiveDataName', $fileName . '*'))
                ->setBoost(self::BOOST_PREFIX)
        );
        
        $group2->addShould(
            (new Wildcard('message.fileMessages.sensitiveDataName', '*' . $fileName))
                ->setBoost(self::BOOST_PARTIAL)
        );

        // Set minimum should match to ensure at least one condition is met
        $group2->setMinimumShouldMatch(1);

        $boolQuery->addMust($group2);
    }

    /**
     * Add date range filter for current month
     */
    private function addDateRangeFilter(BoolQuery $boolQuery): void
    {
        $startOfMonth = new DateTimeImmutable('first day of this month');
        $endOfMonth = new DateTimeImmutable('last day of this month');

        $rangeQuery = new Range('dateCreation', [
            'gte' => $startOfMonth->format('Y-m-d'),
            'lte' => $endOfMonth->format('Y-m-d'),
        ]);

        $boolQuery->addFilter($rangeQuery);
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

    /**
     * Remove search criteria if not needed
     */
    private function removeSearchCriteria(SearchMessage $criteria): void
    {
        $this->em->remove($criteria);
        $this->em->flush();
    }
}
