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

class SearchDiscussions extends AbstractController
{
    const LIMIT = 1000;

    public function __construct(
        private readonly PaginatedFinderInterface $finder,
        private EntityManagerInterface $em,
    )
    {}

    public function findDiscussions(
        User $user,
        SearchDiscussion|null $criteria,
        bool $saveSearch
    ) : array
    {
        // query dsl
        $boolQuery = new BoolQuery();

        $query = new \Elastica\Query();

        if ($user) {
            $boolQuery->addShould(new MatchQuery('personInvitationSender.id', $user->getId()));
            $boolQuery->addShould(new MatchQuery('personInvitationRecipient.id', $user->getId()));
 
            if ($criteria) {
                $name = $criteria->getSensitiveDataName();

                $firstName = $criteria->getSensitiveDataFirstName();

                $createdThisMonth = $criteria->isCreatedThisMonth();

                $group1 = new \Elastica\Query\BoolQuery();
                if ($name !== '') {
                    $group1->addShould(new \Elastica\Query\Wildcard('personInvitationRecipient.name', '*' . $name . '*'));
                    $group1->addShould(new \Elastica\Query\Wildcard('personInvitationRecipient.name', $name . '*'));
                    $group1->addShould(new \Elastica\Query\Wildcard('personInvitationRecipient.name', '*' . $name));
                    $group1->addShould(new \Elastica\Query\Wildcard('personInvitationSender.name', '*' . $name . '*'));
                    $group1->addShould(new \Elastica\Query\Wildcard('personInvitationSender.name', $name . '*'));
                    $group1->addShould(new \Elastica\Query\Wildcard('personInvitationSender.name', '*' . $name));
                } 
                if ($firstName !== '') {
                    $group1->addShould(new \Elastica\Query\Wildcard('personInvitationSender.firstName', '*' . $firstName . '*'));
                    $group1->addShould(new \Elastica\Query\Wildcard('personInvitationSender.firstName', $firstName . '*'));
                    $group1->addShould(new \Elastica\Query\Wildcard('personInvitationSender.firstName', '*' . $firstName));
                    $group1->addShould(new \Elastica\Query\Wildcard('personInvitationRecipient.firstName', '*' . $firstName . '*'));
                    $group1->addShould(new \Elastica\Query\Wildcard('personInvitationRecipient.firstName', $firstName . '*'));
                    $group1->addShould(new \Elastica\Query\Wildcard('personInvitationRecipient.firstName', '*' . $firstName));
                }
                $boolQuery->addMust($group1);

                if ($createdThisMonth === true) {
                    $range = new Range('dateCreation', [
                        'gte' => (new DateTimeImmutable('-1 month'))->format('Y-m-d')
                    ]);
    
                    $boolQuery->addFilter($range);
                }

                if ($saveSearch == false) {
                    $this->em->remove($criteria);
                    $this->em->flush();
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

                $numberUnreadMessages = $discussion->getPersonInvitationSenderNumberUnreadMessages() + $discussion->getPersonInvitationRecipientNumberUnreadMessages();

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
            $group1->addMust(new \Elastica\Query\Term(['personInvitationSender.id' => $user->getId()]));
            // Ajouter la condition "is not null"
            $group1->addMust(new \Elastica\Query\Exists('personInvitationRecipientNumberUnreadMessages'));

            // Groupe 2 (AND logique)
            $group2 = new \Elastica\Query\BoolQuery();
            $group2->addMust(new \Elastica\Query\Term(['personInvitationRecipient.id' => $user->getId()]));
            $group2->addMust(new \Elastica\Query\Exists('personInvitationSenderNumberUnreadMessages'));

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
