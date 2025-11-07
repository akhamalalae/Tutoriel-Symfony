<?php
namespace App\Contracts\Message;

use App\Entity\Discussion;
use App\Entity\SearchMessage;
use App\Entity\User;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

interface MessageSearchInterface
{
    public function messages(int $idDiscussion, int $page, ?SearchMessage $criteria): array;
}
