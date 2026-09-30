<?php

declare(strict_types=1);

namespace App\Contract;

abstract class ApiDocTag
{
    public const DASHBOARD = '01 Dashboard';
    public const SOURCE    = '02 Sources';
    public const GRAB_LOG  = '03 Grab Logs';
    public const COMPANY   = '04 Companies';
    public const VACANCY   = '05 Vacancies';
    public const NOTE      = '06 Notes';
    public const GENERAL   = '99 General';
}
