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
    const LIMIT = 1000;

    public function __construct(
        private readonly PaginatedFinderInterface $finder,
        private EntityManagerInterface $em,
    )
    {}

    public function findMessages(
        User $user,
        Discussion $discussion,
        SearchMessage|null $criteria,
        bool $saveSearch) : array
    {
        $boolQuery = new BoolQuery();

        if ($discussion) {
            $boolQuery->addFilter(new MatchQuery('discussion.id', $discussion?->getId()));
        }

        if ($criteria) {
            $message = $criteria->getSensitiveDataMessage();

            $createdThisMonth = $criteria->isCreatedThisMonth();

            $fileName = $criteria->getSensitiveDataFileName();
            
            if ($message !== '') {
                $group1 = new BoolQuery();
                $group1->addShould(new Wildcard('message.sensitiveDataMessage', '*' . $message . '*'));
                $group1->addShould(new Wildcard('message.sensitiveDataMessage', $message . '*'));
                $group1->addShould(new Wildcard('message.sensitiveDataMessage', '*' . $message));
                $group1->addShould(new MatchPhrase('message.sensitiveDataMessage', $message));
                $boolQuery->addMust($group1);
            } 

            if ($fileName !== '') {
                $group2 = new BoolQuery();
                $group2->addShould(new Wildcard('message.fileMessages.sensitiveDataName', '*' . $fileName . '*'));
                $group2->addShould(new Wildcard('message.fileMessages.sensitiveDataName', $fileName . '*'));
                $group2->addShould(new Wildcard('message.fileMessages.sensitiveDataName', '*' . $fileName));
                $group2->addShould(new MatchPhrase('message.fileMessages.sensitiveDataName', $message));
                $boolQuery->addMust($group2);
            }

            if ($createdThisMonth === true) {
                $rangeQuery = new Range('dateCreation', [
                    'gte' => 'now/M',  // Début du mois
                    'lte' => 'now',    // Date actuelle
                ]);

                $boolQuery->addFilter($rangeQuery);
            }

            if ($saveSearch == false) {
                $this->em->remove($criteria);
                $this->em->flush();
            }
        }

        $query = new Query();

        $query->addSort(array('dateCreation' => array('order' => 'DESC')));

        $query->setQuery($boolQuery);

        return $this->finder->find($query, self::LIMIT);
    }
}
