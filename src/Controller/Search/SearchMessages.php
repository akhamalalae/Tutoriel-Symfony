<?php

namespace App\Controller\Search;

use App\Form\Type\Search\SearchFormType;
use DateTimeImmutable;
use Elastica\Query\BoolQuery;
use Elastica\Query\MatchPhrase;
use Elastica\Query\MatchQuery;
use Elastica\Query\Range;
use App\Entity\User;
use App\Entity\Discussion;
use App\Entity\DiscussionMessageUser;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchMessages extends AbstractController
{
    public function __construct(
        private readonly PaginatorInterface $paginator,
        private readonly PaginatedFinderInterface $finder,
    )
    {}

    public function findMessages(User $user, Discussion $discussion)
    {
        dump($discussion);
        // query dsl
        $boolQuery = new BoolQuery();

        if($discussion) {
            //$userQuery = new \Elastica\Query\Terms('discussion.id', [$discussion?->getId()]);
            //$boolQuery->addShould($userQuery);
            $boolQuery->addMust(new MatchPhrase('discussion.id', $discussion?->getId()));
        }

        $results = $this->finder->createPaginatorAdapter($boolQuery);
        
        return $this->paginator->paginate($results, 1, 10,[
                'defaultSortFieldName' => 'dateCreation',
                'defaultSortDirection' => 'asc'
            ]
        );
    }
}
