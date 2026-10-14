<?php

declare(strict_types=1);

namespace App\Domain\Source\Entity;

enum SourceGrabber: string
{
    case Dou    = 'dou';
    case Djinni = 'djinni';
}
