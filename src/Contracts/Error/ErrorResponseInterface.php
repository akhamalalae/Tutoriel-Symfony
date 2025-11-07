<?php
namespace App\Contracts\Error;

use Throwable;
use Symfony\Component\HttpFoundation\JsonResponse;

interface ErrorResponseInterface
{
    /**
     * Crée une réponse JSON d’erreur à partir d’une exception.
     *
     * @param Throwable $exception
     * @param int|null $statusCode
     * @return JsonResponse
     */
    public function createErrorResponse(Throwable $exception, ?int $statusCode = null): JsonResponse;
}