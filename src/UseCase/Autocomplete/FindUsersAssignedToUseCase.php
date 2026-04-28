<?php

namespace App\UseCase\Autocomplete;

use App\Trait\Search\BaseSearchTrait;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Elastica\Query;
use Elastica\Query\BoolQuery;

class FindUsersAssignedToUseCase
{
    use BaseSearchTrait;

    public function __construct(private readonly PaginatedFinderInterface $finder) {}

    public function execute(string $q): array
    {
        $boolQuery = new BoolQuery();

        if ($q) {
            $this->multiTermSearchQuery($boolQuery, 'sensitiveDataName', $q);
            $this->multiTermSearchQuery($boolQuery, 'sensitiveDataFirstName', $q);
        }

        $query = new Query($boolQuery);
        $paginator = $this->finder->findPaginated($query);
        $paginator->setMaxPerPage(100); 

        return iterator_to_array($paginator->getCurrentPageResults());
    }
}