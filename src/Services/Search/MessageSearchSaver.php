<?php

namespace App\Services\Search;

use App\Services\Search\AbstractSearchSaver;

class MessageSearchSaver extends AbstractSearchSaver
{
    protected function validateCriteria(array $criteria): void
    {
        $required = ['saveSearch', 'createdThisMonth', 'fileName', 'message', 'description'];

        foreach ($required as $field) {
            if (!isset($criteria[$field])) {
                throw new \InvalidArgumentException("Missing required field: $field");
            }
        }
    }
}
