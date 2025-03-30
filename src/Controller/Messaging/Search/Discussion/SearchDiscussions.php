<?php

namespace App\Controller\Messaging\Search\Discussion;

use Elastica\Query\BoolQuery;
use Elastica\Query\Match;
use App\Entity\User;
use App\Entity\SearchDiscussion;
use Elastica\Query\MatchPhrase;
use Elastica\Query\MatchQuery;
use Elastica\Query\Range;
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

class SearchDiscussions extends AbstractController implements SearchDiscussionsInterface
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
     * Find discussions based on search criteria
     */
    public function findDiscussions(
        User $user,
        ?SearchDiscussion $criteria,
        bool $saveSearch
    ): array {
        if (!$user) {
            return [];
        }

        $boolQuery = new BoolQuery();
        $this->addUserFilter($boolQuery, $user);

        if ($criteria) {
            $this->addSearchCriteria($boolQuery, $criteria);

            if (!$saveSearch) {
                $this->removeSearchCriteria($criteria);
            }
        }

        return $this->executeSearch($boolQuery, $user);
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

        $group1 = new BoolQuery();

        if ($name !== '') {
            $this->addNameSearch($group1, $name);
        }

        if ($firstName !== '') {
            $this->addFirstNameSearch($group1, $firstName);
        }

        $boolQuery->addMust($group1);

        if ($createdThisMonth) {
            $this->addDateRangeFilter($boolQuery);
        }
    }

    /**
     * Add name search criteria
     */
    private function addNameSearch(BoolQuery $group1, string $name): void
    {
        // Recherche dans le nom du destinataire
        $group1->addShould(
            (new Wildcard('personInvitationRecipient.name', '*' . $name . '*'))
                ->setBoost(self::BOOST_PARTIAL)
        );
        $group1->addShould(
            (new Wildcard('personInvitationRecipient.name', $name . '*'))
                ->setBoost(self::BOOST_PREFIX)
        );
        $group1->addShould(
            (new Wildcard('personInvitationRecipient.name', '*' . $name))
                ->setBoost(self::BOOST_PARTIAL)
        );

        // Recherche dans le nom de l'expéditeur
        $group1->addShould(
            (new Wildcard('personInvitationSender.name', '*' . $name . '*'))
                ->setBoost(self::BOOST_PARTIAL)
        );
        $group1->addShould(
            (new Wildcard('personInvitationSender.name', $name . '*'))
                ->setBoost(self::BOOST_PREFIX)
        );
        $group1->addShould(
            (new Wildcard('personInvitationSender.name', '*' . $name))
                ->setBoost(self::BOOST_PARTIAL)
        );
    }

    /**
     * Add first name search criteria
     */
    private function addFirstNameSearch(BoolQuery $group1, string $firstName): void
    {
        // Recherche dans le prénom de l'expéditeur
        $group1->addShould(
            (new Wildcard('personInvitationSender.firstName', '*' . $firstName . '*'))
                ->setBoost(self::BOOST_PARTIAL)
        );
        $group1->addShould(
            (new Wildcard('personInvitationSender.firstName', $firstName . '*'))
                ->setBoost(self::BOOST_PREFIX)
        );
        $group1->addShould(
            (new Wildcard('personInvitationSender.firstName', '*' . $firstName))
                ->setBoost(self::BOOST_PARTIAL)
        );

        // Recherche dans le prénom du destinataire
        $group1->addShould(
            (new Wildcard('personInvitationRecipient.firstName', '*' . $firstName . '*'))
                ->setBoost(self::BOOST_PARTIAL)
        );
        $group1->addShould(
            (new Wildcard('personInvitationRecipient.firstName', $firstName . '*'))
                ->setBoost(self::BOOST_PREFIX)
        );
        $group1->addShould(
            (new Wildcard('personInvitationRecipient.firstName', '*' . $firstName))
                ->setBoost(self::BOOST_PARTIAL)
        );
    }

    /**
     * Add date range filter for current month
     */
    private function addDateRangeFilter(BoolQuery $boolQuery): void
    {
        $startOfMonth = new DateTimeImmutable('first day of this month');
        $endOfMonth = new DateTimeImmutable('last day of this month');

        $range = new Range('dateCreation', [
            'gte' => $startOfMonth->format('Y-m-d'),
            'lte' => $endOfMonth->format('Y-m-d')
        ]);

        $boolQuery->addFilter($range);
    }

    /**
     * Execute the search query
     */
    private function executeSearch(BoolQuery $boolQuery, User $user): array
    {
        $query = new \Elastica\Query();
        
        $query->setQuery($boolQuery)
            ->addSort([
                'dateModification' => ['order' => 'DESC']
            ]);

        return [
            "discussions" => $this->finder->find($query, self::LIMIT),
            "countActiveDiscussions" => count($this->findDiscussionNavBar($user))
        ];
    }

    /**
     * Find unread messages for navbar
     */
    public function findMessagesNavBar(User $user): array
    {
        if (!$user) {
            return [];
        }

        $messagesNavBar = $this->findDiscussionNavBar($user);
        $arrayUnreadMessages = [];

        foreach ($messagesNavBar as $discussion) {
            $discussionMessageUsers = $discussion->getDiscussionMessageUsers()->getValues();
            $numberUnreadMessages = $discussion->getPersonInvitationSenderNumberUnreadMessages() + 
                                  $discussion->getPersonInvitationRecipientNumberUnreadMessages();

            for ($i = 0; $i < $numberUnreadMessages; $i++) {
                if (array_key_exists($i, $discussionMessageUsers)) {
                    $arrayUnreadMessages[] = $discussionMessageUsers[$i];
                }
            }
        }

        return $arrayUnreadMessages;
    }

    /**
     * Find discussions for navbar
     */
    public function findDiscussionNavBar(User $user): array
    {
        if (!$user) {
            return [];
        }

        $boolQuery = new BoolQuery();

        // Groupe 1: Discussions où l'utilisateur est l'expéditeur
        $group1 = new BoolQuery();
        $group1->addMust(new Term(['personInvitationSender.id' => $user->getId()]))
               ->addMust(new Exists('personInvitationRecipientNumberUnreadMessages'));

        // Groupe 2: Discussions où l'utilisateur est le destinataire
        $group2 = new BoolQuery();
        $group2->addMust(new Term(['personInvitationRecipient.id' => $user->getId()]))
               ->addMust(new Exists('personInvitationSenderNumberUnreadMessages'));

        // Ajouter les groupes dans une clause OR (should)
        $boolQuery->addShould($group1)
                 ->addShould($group2)
                 ->setMinimumShouldMatch(1);

        $query = new \Elastica\Query();
        $query->setQuery($boolQuery)
              ->addSort([
                  'dateModification' => ['order' => 'DESC'],
                  'dateCreation' => ['order' => 'DESC']
              ]);

        return $this->finder->find($query, self::LIMIT);
    }

    /**
     * Remove search criteria if not needed
     */
    private function removeSearchCriteria(SearchDiscussion $criteria): void
    {
        $this->em->remove($criteria);
        $this->em->flush();
    }
}
