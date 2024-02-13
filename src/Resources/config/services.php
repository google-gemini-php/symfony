<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Gemini;
use Gemini\Client;
use Gemini\Factory;
use Symfony\Component\HttpClient\Psr18Client;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('gemini.http_client', Psr18Client::class)
        ->arg(0, service('http_client'))

        ->set(Factory::class)
        ->factory([Gemini::class, 'factory'])
        ->call('withHttpClient', [service('gemini.http_client')])

        ->set(Client::class)
        ->factory([service(Factory::class), 'make'])
        ->alias('gemini', Client::class);
};
