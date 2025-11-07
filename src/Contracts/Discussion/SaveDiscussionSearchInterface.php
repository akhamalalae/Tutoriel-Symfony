<?php

namespace App\Contracts\Discussion;

use App\Entity\SearchDiscussion;

interface SaveDiscussionSearchInterface
{
    public function saveSearch(?array $criteria): ?SearchDiscussion;
}
