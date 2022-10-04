<?php

namespace Mia\GoToLog\Factory;

use Mia\GoToLog\Service\MiaGotoService;
use Psr\Container\ContainerInterface;

class MiaGotoInitFactory
{
    public function __invoke(ContainerInterface $container, $requestName)
    {
        // Get service
        $service = $container->get(MiaGotoService::class);
        // Generate class
        return new $requestName($service);
    }
}