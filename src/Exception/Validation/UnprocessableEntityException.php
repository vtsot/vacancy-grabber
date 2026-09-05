<?php

declare(strict_types=1);

namespace App\Exception\Validation;

use App\Exception\AppException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class UnprocessableEntityException extends AppException
{
    public function __construct(
        string $message = 'Unprocessable Entity',
        int $code = Response::HTTP_UNPROCESSABLE_ENTITY,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
