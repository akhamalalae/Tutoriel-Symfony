<?php
namespace App\Contracts\Discussion;

use Symfony\Component\HttpFoundation\JsonResponse;

interface DiscussionDeleterInterface
{
    public function deleteDiscussion(int $id): JsonResponse;
    public function deleteSearchDiscussion(int $id): JsonResponse;
}
