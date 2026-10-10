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

    public const int TIMEOUT = 5;

    public function wait(?int $timeout = self::TIMEOUT): void
    {
        $I = $this;
        $I->parentWait($timeout);
    }

    public function loginAsUser(?string $username, ?string $password): void
    {
        $I = $this;
        $I->amOnPage('/auth/login');
        $I->see('Username');
        $I->see('Password');
        $I->see('Login');
//        $I->seeInFormFields('form', ['_username' => '', '_password' => '']);
//        $I->submitForm('form', [
//            '_username' => $username ?? null,
//            '_password' => $password ?? null,
//        ],             'submit');
//        $I->wait();
    }

    public function tryLoginToAdmin(?string $username, ?string $password): void
    {
        $I = $this;
        $I->amOnPage('/admin/login');
        $I->see('Login');
        $I->seeInFormFields('form', ['_username' => '', '_password' => '']);
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
