<?php

declare(strict_types=1);

namespace App\Component\General\Exception\Validation;

use App\Component\General\Exception\AppException;
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
