<?php
namespace App\Contracts\Discussion;

use App\Entity\User;
use App\Entity\Discussion;
use App\Entity\SearchDiscussion;
use Symfony\Component\Form\FormInterface;

interface DiscussionUnreadMessagesInterface
{
    public function getDiscussionUnreadMessages(array $discussions, User $user): array;
}