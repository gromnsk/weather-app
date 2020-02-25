<?php
declare(strict_types=1);

namespace App\Application\Actions\Weather;

use App\Domain\Weather\Scale;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class GetByDate extends WeatherAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $city = $this->resolveArg('city');
        $date = date('Ymd', strtotime($this->resolveArg('date')));
        if (strtotime($date) < strtotime('now') || strtotime($date) > strtotime('+10 days')) {
            throw new HttpBadRequestException($this->request, 'You can get weather only for last 10 days');
        }
        $time = date('H:00', strtotime($this->resolveArg('time')));
        $scale = $this->args['scale'] ?? Scale::CELSIUS_SCALE;
        if (!Scale::validate($scale)) {
            throw new HttpBadRequestException($this->request, sprintf("invalid %s scale", $scale));
        }
        $weather = $this->weatherRepository->getByDate($city, $date, $time, $scale);

        return $this->respondWithData($weather);
    }
}
