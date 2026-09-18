<?php

declare(strict_types=1);

namespace App\Component\General\Exception\Validation;

use App\Component\General\Exception\AppException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class BadRequestException extends AppException
{
    public function __construct(string $message = 'Bad Request', int $code = Response::HTTP_BAD_REQUEST, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
