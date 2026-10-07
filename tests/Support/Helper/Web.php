<?php

declare(strict_types=1);

namespace App\Tests\Support\Helper;

use Codeception\Exception\ModuleException;
use Codeception\Module;
use Codeception\TestInterface;

class Web extends Module
{
    /**
     * @throws ModuleException
     */
    public function _before(TestInterface $test): void
    {
        //        /** @var REST $rest */
        //        $rest = $this->getModule('REST');
        //        $rest->haveHttpHeader('Content-Type', 'application/json');
        //        $rest->haveHttpHeader('Accept', 'application/json');
        //        $rest->haveHttpHeader('Accept-Language', 'en_GB');
    }

    //    /**
    //     * @param array<mixed> $settings
    //     */
    //    public function _beforeSuite(array $settings = []): void
    //    {
    //        parent::_beforeSuite($settings);
    //
    //        TestMailer::start();
    //
    //        TestTransport::initialize();
    //        TestTransport::enableMessagesCollection();
    //        TestBus::enableMessagesCollection();
    //        TestTransport::disableResetOnKernelShutdown();
    //    }
    //
    //    public function _afterSuite(): void
    //    {
    //        parent::_afterSuite();
    //
    //        TestMailer::stop();
    //
    //        TestTransport::disableMessagesCollection();
    //        TestBus::disableMessagesCollection();
    //        TestTransport::resetAll();
    //        TestBus::resetAll();
    //        TestTransport::enableResetOnKernelShutdown();
    //    }
}
