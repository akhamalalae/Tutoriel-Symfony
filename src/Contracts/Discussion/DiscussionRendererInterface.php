<?php
namespace App\Contracts\Discussion;

use App\Entity\SearchDiscussion;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

interface DiscussionRendererInterface
{
    public function renderDiscussionForm(FormInterface $form): Response;
    public function renderDiscussionMessageNavBar(array $discussions): Response;
    public function renderListDiscussion(int $page, ?SearchDiscussion $searchDiscussion): string;
}