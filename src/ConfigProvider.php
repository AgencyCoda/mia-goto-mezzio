<?php

/**
 * @see       https://github.com/mezzio/mezzio-authorization for the canonical source repository
 * @copyright https://github.com/mezzio/mezzio-authorization/blob/master/COPYRIGHT.md
 * @license   https://github.com/mezzio/mezzio-authorization/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Mia\GoToLog;

use Mia\GoToLog\Factory\MiaGotoInitFactory;
use Mia\GoToLog\Factory\MiaGotoServiceFactory;
use Mia\GoToLog\Handler\GenerateAccessTokenHandler;
use Mia\GoToLog\Handler\GetMeHandler;
use Mia\GoToLog\Handler\GotoGenerateUrlHandler;
use Mia\GoToLog\Service\MiaGotoService;

class ConfigProvider
{
    /**
     * Return the configuration array.
     */
    public function __invoke() : array
    {
        return [
            'dependencies'  => $this->getDependencies()
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies() : array
    {
        return [
            'factories' => [
                MiaGotoService::class => MiaGotoServiceFactory::class,

                GotoGenerateUrlHandler::class => MiaGotoInitFactory::class,
                GenerateAccessTokenHandler::class => MiaGotoInitFactory::class,
                GetMeHandler::class => MiaGotoInitFactory::class
            ],
        ];
    }
}