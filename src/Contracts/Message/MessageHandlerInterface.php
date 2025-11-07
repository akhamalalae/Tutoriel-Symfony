<?php
namespace App\Contracts\Message;

use App\Entity\Discussion;
use App\Entity\User;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

interface MessageHandlerInterface
{
    public function handleMessageFormData(FormInterface $form, int $idDiscussion): JsonResponse;
    public function markUnreadMessagesAsRead(int $idDiscussion, User $user);
}
