<?php

declare(strict_types=1);

namespace App\Tests\Web;

//use App\Entity\Page\AbstractPage;
//use App\Tests\Factory\Page\PageTranslationFactory;
//use App\Tests\Factory\SystemPageFactory;
//use App\Tests\Factory\UserFactory;
//use App\Tests\Support\FunctionalTester;
//
//use function Zenstruck\Foundry\faker;

use App\Tests\Support\FunctionalTester;
use App\Tests\Support\WebTester;

class HomepageControllerCest
{
    public function homepageRedirectsSuccess(WebTester $I): void
    {
        $I->stopFollowingRedirects();
        $I->amOnPage('/');
        $I->seeResponseCodeIsRedirection();
        $I->startFollowingRedirects();
    }

    public function homepageSuccess(WebTester $I): void
    {
        $I->amOnPage('/');
        $I->seeCurrentUrlEquals('/auth/login');
    }


//    public function cmsHomePageSuccessAsGuest(FunctionalTester $I): void
//    {
//        return;// skipped
//
//        $title    = faker()->text(10);
//        $homepage = SystemPageFactory::createOne(['slug' => 'homepage', 'active' => true]);
//        PageTranslationFactory::createOne(['translatable' => $homepage, 'title' => $title]);
//
//        $I->amOnPage('/');
//        $I->seeResponseCodeIs(500);
//        //$I->see($title, 'title');
//    }
//
//    public function cmsHomePageRedirectToDashboardFailed(FunctionalTester $I): void
//    {
//        return;// skipped
//
//        $user = UserFactory::createValidUser();
//        $I->amLoggedInAs($user->object());
//
//        $title    = faker()->text(10);
//        $homepage = SystemPageFactory::createOne(['slug' => 'homepage', 'active' => true]);
//        PageTranslationFactory::createOne(['translatable' => $homepage, 'title' => $title]);
//
//        $I->amOnPage('/');
//        $I->seeResponseCodeIs(500);
//        $I->see('Page "users/me/dashboard" not found', 'title');
//    }
//
//    public function cmsHomePageRedirectToDashboardSuccess(FunctionalTester $I): void
//    {
//        $user = UserFactory::createValidUser();
//
//        $I->amLoggedInAs($user->object());
//
//        $dashboardTitle = faker()->text(10);
//
//        $homepage = SystemPageFactory::createOne(['slug' => 'homepage', 'active' => true]);
//        PageTranslationFactory::createOne(['translatable' => $homepage, 'title' => faker()->text(30)]);
//
//        $dashboard = SystemPageFactory::createOne(['slug' => 'users/me/dashboard', 'active' => true]);
//        PageTranslationFactory::createOne(['translatable' => $dashboard, 'title' => $dashboardTitle]);
//
//        $I->amOnPage('/');
//        $I->seeResponseCodeIs(200);
//        $I->see($dashboardTitle, 'title');
//    }
//
//    public function cmsDetailsPageSuccess(FunctionalTester $I): void
//    {
//        $user = UserFactory::createValidUser();
//        $I->amLoggedInAs($user->object());
//
//        $me          = SystemPageFactory::findOrCreate(['slug' => 'users/me', 'active' => true]);
//        $translation = PageTranslationFactory::findOrCreate(['translatable' => $me]);
//
//        $I->amOnPage('/en-gb/users/me');
//        $I->seeResponseCodeIs(200);
//        $I->see($translation->getTitle(), 'title');
//    }
//
//    public function cmsDetailsPageForDifferentUserSuccess(FunctionalTester $I): void
//    {
//        $user = UserFactory::createValidUser();
//        $I->amLoggedInAs($user->object());
//
//        $user = UserFactory::createValidUser();
//
//        $me          = SystemPageFactory::findOrCreate(['slug' => 'users/me', 'active' => true]);
//        $translation = PageTranslationFactory::findOrCreate(['translatable' => $me]);
//
//        $I->amOnPage('/en-gb/users/' . $user->getId()->toString());
//        $I->seeResponseCodeIs(200);
//        $I->see($translation->getTitle(), 'title');
//    }
//
//    public function cmsBookmarksPageSuccess(FunctionalTester $I): void
//    {
//        $user = UserFactory::createValidUser();
//        $I->amLoggedInAs($user->object());
//
//        $bookmark    = SystemPageFactory::findOrCreate(['slug' => 'users/me/bookmarks', 'active' => true]);
//        $translation = PageTranslationFactory::findOrCreate(['translatable' => $bookmark]);
//
//        $I->amOnPage('/en-gb/users/me/bookmarks');
//        $I->seeResponseCodeIs(200);
//        $I->see($translation->getTitle(), 'title');
//    }
//
//    public function cmsRecentPageSuccess(FunctionalTester $I): void
//    {
//        $user = UserFactory::createValidUser();
//        $I->amLoggedInAs($user->object());
//
//        $recent      = SystemPageFactory::findOrCreate(['slug' => 'users/me/recent', 'active' => true]);
//        $translation = PageTranslationFactory::findOrCreate(['translatable' => $recent]);
//
//        $I->amOnPage('/en-gb/users/me/recent');
//        $I->seeResponseCodeIs(200);
//        $I->see($translation->getTitle(), 'title');
//    }
//
//    public function cmsPageNotFoundFailed(FunctionalTester $I): void
//    {
//        $slug = faker()->slug();
//
//        $I->amOnPage('/en-gb/' . $slug);
//        $I->seeResponseCodeIs(500);
//        //$I->see('Page "' . $slug . '" not found', 'title');
//    }
//
//    public function cmsHomePageEarlyAccessRedirectSuccess(FunctionalTester $I): void
//    {
//        $earlyAccess = SystemPageFactory::createOne(['slug' => AbstractPage::SLUG_EARLY_ACCESS, 'active' => true]);
//        PageTranslationFactory::createOne(['translatable' => $earlyAccess, 'title' => faker()->text(30)]);
//
//        $I->amOnPage('/en-gb');
//        $I->seeResponseCodeIs(200);
//    }
//
//    public function cmsHomePageDashboardRedirectSuccess(FunctionalTester $I): void
//    {
//        $user = UserFactory::createValidUser();
//        $I->amLoggedInAs($user->object());
//
//        $I->amOnPage('/');
//        $I->seePageRedirectsTo('/', '/en-gb/users/me/dashboard');
//    }
}
