<?php

declare(strict_types=1);

namespace App\Component\Vacancy\Contract\Dictionary;

enum VacancyGrabber: string
{
    case Dou    = 'dou';
    case Djinni = 'djinni';
}
