<?php 

declare(strict_types=1);

namespace Mia\GoToLog\Factory;

use Mia\GoToLog\Service\MiaGotoService;
use Psr\Container\ContainerInterface;

class MiaGotoServiceFactory 
{
    public function __invoke(ContainerInterface $container) : MiaGotoService
    {
        // Obtenemos configuracion
        $config = $container->get('config')['goto'];
        // creamos libreria
        return new MiaGotoService($config['client_id'], $config['client_secret']);
    }
}