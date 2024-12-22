<?php

namespace App\Controller\Search;

use Elastica\Query\BoolQuery;
use Elastica\Query\MatchPhrase;
use App\Entity\User;
use App\Entity\Discussion;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchMessages extends AbstractController
{
    public function __construct(
        private readonly PaginatorInterface $paginator,
        private readonly PaginatedFinderInterface $finder,
    )
    {}

    public function findMessages(User $user, Discussion $discussion) : array
    {
        // query dsl
        $boolQuery = new BoolQuery();

        if ($discussion) {
            $boolQuery->addMust(new MatchPhrase('discussion.id', $discussion?->getId()));
        }

        $query = new \Elastica\Query();

        $query->addSort(array('dateCreation' => array('order' => 'DESC')));

        $query->setQuery($boolQuery);

        return $this->finder->find($query);
    }
}
