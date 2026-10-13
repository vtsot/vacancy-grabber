<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Shared\Exception\Validation;

use App\VacancyGrabber\Application\Shared\Exception\AppException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class EntityNotFoundException extends AppException
{
    public function __construct(
        $message = 'GENERAL.NOT_FOUND',
        $code = Response::HTTP_NOT_FOUND,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
