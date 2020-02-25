<?php
declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use App\Application\Actions\Weather\GetByDate;
use App\Application\Actions\Weather\GetTodayAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.+}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Weather app');
        return $response;
    });

    $app->group('/weather', function (Group $group) {
        $group->get('/{city}[/{scale}]', GetTodayAction::class);
        $group->get('/{city}/{date}/{time}[/{scale}]', GetByDate::class);
    });
};
