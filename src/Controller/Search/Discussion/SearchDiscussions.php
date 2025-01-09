<?php

namespace App\Controller\Search\Discussion;

use Elastica\Query\BoolQuery;
use Elastica\Query\Match;
use App\Entity\User;
use Elastica\Query\MatchPhrase;
use Elastica\Query\MatchQuery;
use Elastica\Query\Range;
use App\Entity\Discussion;
use DateTimeImmutable;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchDiscussions extends AbstractController
{
    const LIMIT = 1000;

    public function __construct(
        private readonly PaginatedFinderInterface $finder,
    )
    {}

    public function findDiscussions(User $user, array|null $criteria = []) : array
    {
        // query dsl
        $boolQuery = new BoolQuery();

        $query = new \Elastica\Query();

        if ($user) {
            $boolQuery->addShould(new MatchQuery('personOne.id', $user->getId()));

            $boolQuery->addShould(new MatchQuery('personTwo.id', $user->getId()));

            if ($criteria) {
                $name = $criteria['name'];

                $firstName = $criteria['firstName'];

                $createdThisMonth = $criteria['createdThisMonth'];

                if ($name !== '') {
                    $group1 = new \Elastica\Query\BoolQuery();
                    $group1->addShould(new \Elastica\Query\Wildcard('personTwo.name', '*' . $name . '*'));
                    $group1->addShould(new \Elastica\Query\Wildcard('personTwo.name', $name . '*'));
                    $group1->addShould(new \Elastica\Query\Wildcard('personTwo.name', '*' . $name));
                    $boolQuery->addMust($group1);
                } 
                
                if ($firstName !== '') {
                    $group2 = new \Elastica\Query\BoolQuery();
                    $group2->addShould(new \Elastica\Query\Wildcard('personTwo.firstName', '*' . $firstName . '*'));
                    $group2->addShould(new \Elastica\Query\Wildcard('personTwo.firstName', $firstName . '*'));
                    $group2->addShould(new \Elastica\Query\Wildcard('personTwo.firstName', '*' . $firstName));
                    $boolQuery->addMust($group2);
                }

                if ($createdThisMonth === 'true') {
                    $range = new Range('dateCreation', [
                        'gte' => (new DateTimeImmutable('-1 month'))->format('Y-m-d')
                    ]);
    
                    $boolQuery->addFilter($range);
                }
            }

            $boolQuery->setMinimumShouldMatch(1);

            $query->addSort([
                'dateModification' => array('order' => 'ASC'),
            ]);

            $query->addSort([
                'dateCreation' => array('order' => 'ASC'),
            ]);

            $query->setQuery($boolQuery);

            return [
                "discussions" => $this->finder->find($query, self::LIMIT),
                "countActiveDiscussions" => count($this->findDiscussionNavBar($user))
            ];
        }

        return [];
    }

    public function findMessagesNavBar(User $user) : array
    {
        if ($user) {
            $messagesNavBar = $this->findDiscussionNavBar($user);

            $arrayUnreadMessages = [];

            foreach ($messagesNavBar as $discussion) {
                $discussionMessageUsers = $discussion->getDiscussionMessageUsers()->getValues();

                $numberUnreadMessages = $discussion->getPersonOneNumberUnreadMessages() + $discussion->getPersonTwoNumberUnreadMessages();

                for ($i = 0; $i < $numberUnreadMessages; $i++) {
                    array_push(
                        $arrayUnreadMessages, 
                        $discussionMessageUsers[$i]
                    );
                }
            }

            return $arrayUnreadMessages;
        }

        return [];
    }

    public function findDiscussionNavBar(User $user) : array
    {
        if ($user) {
            $boolQuery = new \Elastica\Query\BoolQuery();

            // Groupe 1 (AND logique)
            $group1 = new \Elastica\Query\BoolQuery();
            $group1->addMust(new \Elastica\Query\Term(['personOne.id' => $user->getId()]));
            // Ajouter la condition "is not null"
            $group1->addMust(new \Elastica\Query\Exists('personTwoNumberUnreadMessages'));

            // Groupe 2 (AND logique)
            $group2 = new \Elastica\Query\BoolQuery();
            $group2->addMust(new \Elastica\Query\Term(['personTwo.id' => $user->getId()]));
            $group2->addMust(new \Elastica\Query\Exists('personOneNumberUnreadMessages'));

            // Ajouter les groupes dans une clause OR (should)
            $boolQuery->addShould($group1);
            $boolQuery->addShould($group2);

            // Minimum 1 groupe doit correspondre
            $boolQuery->setMinimumShouldMatch(1);

            $query = new \Elastica\Query();

            $query->addSort([
                'dateModification' => array('order' => 'DESC'),
            ]);

            $query->addSort([
                'dateCreation' => array('order' => 'DESC'),
            ]);

            $query->setQuery($boolQuery);

            return $this->finder->find($query, self::LIMIT);
        }

        return [];
    }
}
