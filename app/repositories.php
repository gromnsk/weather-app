<?php
declare(strict_types=1);

use App\Domain\Weather\WeatherRepositories;
use App\Domain\Weather\WeatherRepository;
use App\Domain\Weather\WeatherRepositoryCacheDecorator;
use App\Infrastructure\External\Weather\BBCRepository;
use App\Infrastructure\External\Weather\IAmsterdamRepository;
use App\Infrastructure\External\Weather\WeatherComRepository;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        WeatherRepository::class => function (ContainerInterface $c) {
            $settings = $c->get('settings');
            $repositories = new WeatherRepositories();
            $repositories->add(new BBCRepository($settings['weatherAPIs']['bbc']['path']));
            $repositories->add(new IAmsterdamRepository($settings['weatherAPIs']['iAmsterdam']['path']));
            $repositories->add(new WeatherComRepository($settings['weatherAPIs']['weatherCom']['path']));

            $cachierClient = new Predis\Client($settings['cache']['redis']);

            return new WeatherRepositoryCacheDecorator($repositories, $cachierClient, $settings['cache']['redis']['ttl']);
        },
    ]);
};
