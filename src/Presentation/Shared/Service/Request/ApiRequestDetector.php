<?php

declare(strict_types=1);

namespace App\Presentation\Shared\Service\Request;

use Symfony\Component\HttpFoundation\Request;

use function in_array;
use function str_contains;

readonly class ApiRequestDetector
{
    public function isApiRequest(Request $request): bool
    {
        return $request->isXmlHttpRequest()
            || $request->getContentTypeFormat() === 'json'
            || in_array('application/json', $request->getAcceptableContentTypes(), true)
            || str_contains($request->getPathInfo(), '/api/');
    }
}
