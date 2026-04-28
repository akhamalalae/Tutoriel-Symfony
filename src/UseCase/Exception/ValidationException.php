<?php

namespace App\UseCase\Exception;

class ValidationException extends \Exception
{
    public function __construct(private array $errors)
    {
        parent::__construct('Validation error');
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}