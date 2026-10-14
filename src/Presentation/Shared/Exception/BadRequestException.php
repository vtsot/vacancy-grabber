<?php

declare(strict_types=1);

namespace App\Presentation\Shared\Exception;

use App\Application\Exception\AppException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class BadRequestException extends AppException
{
    public function __construct(
        string $message = 'GENERAL.BAD_REQUEST',
        int $code = Response::HTTP_BAD_REQUEST,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
