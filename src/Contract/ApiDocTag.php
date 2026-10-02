<?php

declare(strict_types=1);

namespace App\Contract;

abstract class ApiDocTag
{
    public const string AUTH      = '01 Auth';
    public const string DASHBOARD = '02 Dashboard';
    public const string SOURCE    = '03 Sources';
    public const string GRAB_LOG  = '04 Grab Logs';
    public const string COMPANY   = '05 Companies';
    public const string VACANCY   = '06 Vacancies';
    public const string NOTE      = '07 Notes';
    public const string GENERAL   = '99 General';
}
