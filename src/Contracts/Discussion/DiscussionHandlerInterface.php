<?php
namespace App\Contracts\Discussion;

use App\Entity\User;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

interface DiscussionHandlerInterface
{
    public function handleDiscussionFormData(FormInterface $form): JsonResponse;
}
