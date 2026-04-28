<?php

namespace App\DTO\Ticket;

final class Software
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?int $productId,
        public readonly ?string $comment,
        public readonly ?string $licenseKey,
        public readonly ?string $expiresAt,
    ) {}
}