<?php
namespace App\Services\Error;

use App\Contracts\Error\ErrorResponseInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

class ErrorResponseService implements ErrorResponseInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator
    ) {}

    public function createErrorResponse(Throwable $exception, ?int $statusCode = null): JsonResponse
    {
        $statusCode ??= JsonResponse::HTTP_INTERNAL_SERVER_ERROR;

        // En environnement dev, tu peux inclure le message d’erreur pour debug
        $isDev = $_ENV['APP_ENV'] === 'dev';

        $data = [
            'error' => $this->translator->trans('An error occurred'),
        ];

        if ($isDev) {
            $data['details'] = $exception->getMessage();
            $data['trace'] = $exception->getTraceAsString();
        }

        return new JsonResponse($data, $statusCode);
    }
}
