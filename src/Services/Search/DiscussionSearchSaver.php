<?php

namespace App\Services\Search;

use App\Services\Search\AbstractSearchSaver;

class DiscussionSearchSaver extends AbstractSearchSaver
{
    protected function validateCriteria(array $criteria): void
    {
        $required = ['saveSearch', 'createdThisMonth', 'name', 'description', 'firstName'];

        foreach ($required as $field) {
            if (!isset($criteria[$field])) {
                throw new \InvalidArgumentException("Missing required field: $field");
            }
        }
    }
}
