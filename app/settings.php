<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {
    // Global Settings Object
    $containerBuilder->addDefinitions([
        'settings' => [
            'displayErrorDetails' => true, // Should be set to false in production
            'logger' => [
                'name' => 'slim-app',
                'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                'level' => Logger::DEBUG,
            ],
            'weatherAPIs' => [
                'bbc' => [
                    'path' => __DIR__ . '/../files/bbc.xml',
                ],
                'iAmsterdam' => [
                    'path' => __DIR__ . '/../files/iamsterdam.json',
                ],
                'weatherCom' => [
                    'path' => __DIR__ . '/../files/weathercom.csv',
                ],
            ],
            'cache' => [
                'redis' => [
                    'scheme' => 'tcp',
                    'host' => 'redis',
                    'port' => 6379,
                    'ttl' => 60,
                ],
            ],
        ],
    ]);
};
