<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Presentation\Shared\Exception;

use App\VacancyGrabber\Application\Exception\AppException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class UnprocessableEntityException extends AppException
{
    public function __construct(
        string $message = 'GENERAL.UNPROCESSABLE_ENTITY',
        int $code = Response::HTTP_UNPROCESSABLE_ENTITY,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
