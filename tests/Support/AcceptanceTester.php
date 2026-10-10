<?php

declare(strict_types=1);

namespace App\Tests\Support;

use Codeception\Actor;

/**
 * Inherited Methods
 *
 * @method void wantTo($text)
 * @method void wantToTest($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause($vars = [])
 *
 * @SuppressWarnings(PHPMD)
*/
class AcceptanceTester extends Actor
{
    use _generated\AcceptanceTesterActions {
        wait as parentWait;
    }

    public const int TIMEOUT = 2;

    public function wait(?int $timeout = self::TIMEOUT): void
    {
        $I = $this;
        $I->parentWait($timeout);
    }

    public function tryLoginToFront(?string $username, ?string $password): void
    {
        $I = $this;
        $I->amOnPage('/auth/login');
        $I->wait();
        $I->seeElement('form[name="login-form"]');
        $I->submitForm('form[name="login-form"]', [
            'username' => $username ?? null,
            'password' => $password ?? null,
        ],             'submit');
        $I->wait();
    }

    public function loginToFrontAsUser(string $username, ?string $password = 'password'): void
    {
        $I = $this;
        $I->tryLoginToFront($username, $password);
        $I->wait();
    }

    public function tryLoginToAdmin(?string $username, ?string $password): void
    {
        $I = $this;
        $I->amOnPage('/admin/login');
        $I->wait();
        $I->see('Login');
        $I->seeElement('form[action="/admin/login-check"][method="post"]');
        $I->submitForm('form', [
            '_username' => $username ?? null,
            '_password' => $password ?? null,
        ],             'submit');
        $I->wait();
    }

    public function loginToAdminAsAdmin(string $username, ?string $password = 'password'): void
    {
        $I = $this;
        $I->tryLoginToAdmin($username, $password);
        $I->wait();

        $I->seeCurrentUrlEquals('/admin/dashboard');
        $I->see('Logout');
        $I->seeLink('Logout');
    }
}
