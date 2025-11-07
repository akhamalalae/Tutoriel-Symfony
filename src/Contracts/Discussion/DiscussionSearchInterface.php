<?php

namespace App\Contracts\Discussion;

use App\Entity\SearchDiscussion;

interface DiscussionSearchInterface
{
    public function discussions(int $page, SearchDiscussion|null $criteria): array;
}
