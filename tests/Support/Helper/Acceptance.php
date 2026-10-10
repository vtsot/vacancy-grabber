<?php

declare(strict_types=1);

namespace App\Tests\Support\Helper;

use App\Tests\Support\AcceptanceTester;
use Codeception\Exception\ModuleException;
use Codeception\Module;
use Codeception\Module\WebDriver;
use Codeception\TestInterface;

class Acceptance extends Module
{
    /**
     * @throws ModuleException
     */
    public function _after(TestInterface $test): void
    {
        /** @var WebDriver $webDriver */
        $webDriver = $this->getModule('WebDriver');
        $webDriver->wait(AcceptanceTester::TIMEOUT);
    }
}
