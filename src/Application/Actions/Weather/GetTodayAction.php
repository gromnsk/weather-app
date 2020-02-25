<?php
declare(strict_types=1);

namespace App\Application\Actions\Weather;

use App\Domain\Weather\Scale;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class GetTodayAction extends WeatherAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $city = $this->args['city'] ?? '';
        if (empty($city)) {
            throw new HttpBadRequestException('Empty city');
        }
        $scale = $this->args['scale'] ?? Scale::CELSIUS_SCALE;
        if (!Scale::validate($scale)) {
            throw new HttpBadRequestException(sprintf("invalid %s scale", $scale));
        }
        $weather = $this->weatherRepository->getToday($city, $scale);

        return $this->respondWithData($weather);
    }
}
