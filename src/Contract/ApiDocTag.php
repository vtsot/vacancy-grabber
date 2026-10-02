<?php

declare(strict_types=1);

namespace App\Contract;

abstract class ApiDocTag
{
    public const string DASHBOARD = '01 Dashboard';
    public const string SOURCE    = '02 Sources';
    public const string GRAB_LOG  = '03 Grab Logs';
    public const string COMPANY   = '04 Companies';
    public const string VACANCY   = '05 Vacancies';
    public const string NOTE      = '06 Notes';
    public const string GENERAL   = '99 General';
}
