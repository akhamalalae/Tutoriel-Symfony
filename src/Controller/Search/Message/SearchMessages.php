<?php

namespace App\Controller\Search\Message;

use Elastica\Query\BoolQuery;
use Elastica\Query\MatchQuery;
use App\Entity\User;
use App\Entity\SearchMessage;
use App\Entity\Discussion;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Elastica\Query\Range;
use DateTimeImmutable;

class SearchMessages extends AbstractController
{
    const LIMIT = 100;

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
        // query dsl
        $boolQuery = new BoolQuery();

        if ($discussion) {
            $boolQuery->addFilter(new MatchQuery('discussion.id', $discussion?->getId()));
        }

        if ($criteria) {
            $message = $criteria->getSensitiveDataMessage();

            $createdThisMonth = $criteria->isCreatedThisMonth();

            $fileName = $criteria->getSensitiveDataFileName();
            
            if ($message !== '') {
                $group1 = new \Elastica\Query\BoolQuery();
                $group1->addShould(new \Elastica\Query\Wildcard('message.sensitiveDataMessage', '*' . $message . '*'));
                $group1->addShould(new \Elastica\Query\Wildcard('message.sensitiveDataMessage', $message . '*'));
                $group1->addShould(new \Elastica\Query\Wildcard('message.sensitiveDataMessage', '*' . $message));
                $boolQuery->addMust($group1);
            } 

            if ($fileName !== '') {
                $group2 = new \Elastica\Query\BoolQuery();
                $group2->addShould(new \Elastica\Query\Wildcard('message.fileMessages.sensitiveDataName', '*' . $fileName . '*'));
                $group2->addShould(new \Elastica\Query\Wildcard('message.fileMessages.sensitiveDataName', $fileName . '*'));
                $group2->addShould(new \Elastica\Query\Wildcard('message.fileMessages.sensitiveDataName', '*' . $fileName));
                $boolQuery->addMust($group2);
            } 

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

        $query = new \Elastica\Query();

        $query->addSort(array('dateCreation' => array('order' => 'DESC')));

        $query->setQuery($boolQuery);

        return $this->finder->find($query, self::LIMIT);
    }
}
