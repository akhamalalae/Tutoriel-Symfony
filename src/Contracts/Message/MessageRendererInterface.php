<?php
namespace App\Contracts\Message;

use App\Entity\User;
use App\Entity\Discussion;
use App\Entity\SearchMessage;
use Symfony\Component\Form\FormInterface;

interface MessageRendererInterface
{
    public function renderForm(FormInterface $form): string;
    public function renderMessages(int $idDiscussion, int $page, ?SearchMessage $searchMessage): string;
}