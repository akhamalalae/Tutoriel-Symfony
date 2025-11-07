<?php
namespace App\EventListener\Contracts\Activity;

use App\Entity\SearchDiscussion;

interface ActivitySearchDiscussionInterface
{
    public function decryptSearchDiscussion(SearchDiscussion $searchDiscussion): void;
    public function encryptSearchDiscussion(SearchDiscussion $searchDiscussion): void;
}