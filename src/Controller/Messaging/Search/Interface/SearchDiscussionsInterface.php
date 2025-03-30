<?php

namespace App\Controller\Messaging\Search\Interface;

use App\Entity\SearchDiscussion;
use App\Entity\User;

interface SearchDiscussionsInterface
{
    public function findDiscussions(User $user, SearchDiscussion|null $criteria, bool $saveSearch): array;
    public function findMessagesNavBar(User $user): array;
}