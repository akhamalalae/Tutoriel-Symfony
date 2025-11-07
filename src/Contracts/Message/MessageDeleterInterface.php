<?php
namespace App\Contracts\Message;

use Symfony\Component\HttpFoundation\JsonResponse;

interface MessageDeleterInterface
{
    public function deleteMessage(int $id): JsonResponse;
    public function deleteSearchMessage(int $id): JsonResponse;
}
