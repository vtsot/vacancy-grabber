<?php

declare(strict_types=1);

namespace App\Contract;

abstract class ApiDocTag
{
    public const SOURCE   = '01 Sources';
    public const GRAB_LOG = '02 Grab Logs';
    public const COMPANY  = '03 Companies';
    public const VACANCY  = '04 Vacancies';
    public const NOTE     = '05 Notes';
    public const GENERAL  = '99 General';
}
