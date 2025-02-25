<?php

namespace App\Exception;

use RuntimeException;

class ValidationException extends RuntimeException
{
    private array $errors;

    public function __construct(array $errors = [], int $statusCode = 422, string $message = 'Validation failed')
    {
        $this->errors = $errors;
        parent::__construct($message, $statusCode);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
