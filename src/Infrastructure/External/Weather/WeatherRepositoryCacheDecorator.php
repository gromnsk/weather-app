<?php
declare(strict_types=1);

namespace App\Infrastructure\External\Weather;

use App\Domain\DomainException\DomainInvalidRequestException;
use App\Domain\Weather\Scale;
use App\Domain\Weather\Weather;
use App\Domain\Weather\WeatherRepository;
use Predis\ClientInterface;

class WeatherRepositoryCacheDecorator implements WeatherRepository
{
    /** @var WeatherRepository */
    protected $repository;

    /** @var ClientInterface */
    protected $cachierClient;

    /** @var int */
    protected $ttl;

    /**
     * WeatherRepositoryCacheDecorator constructor.
     * @param WeatherRepository $repository
     * @param ClientInterface $cachierClient
     * @param int $ttl
     */
    public function __construct(WeatherRepository $repository, ClientInterface $cachierClient, int $ttl = 0)
    {
        $this->repository = $repository;
        $this->cachierClient = $cachierClient;
        $this->ttl = $ttl;
    }

    /**
     * @param string $city
     * @param string $date
     * @param string $time
     * @param string $scale
     * @return Weather
     * @throws DomainInvalidRequestException
     */
    public function getByDate(string $city, string $date, string $time, string $scale = ''): Weather
    {
        $cachedValue = $this->cachierClient->get($this->getKey([
            'city' => $city,
            'date' => $date,
            'time' => $time,
        ]));
        if (!empty($cachedValue)) {
            $weather = new Weather(Scale::CELSIUS_SCALE, $city, $date, $time, (int)$cachedValue);
        } else {
            $weather = $this->repository->getByDate($city, $date, $time, $scale);
            $key = $this->getKey($weather->jsonSerialize());
            $this->cachierClient->set($key, $weather->getValue());
            $this->cachierClient->expire($key, $this->ttl);
        }

        $value = Scale::convert($weather->getScale(), $scale, $weather->getValue());
        $convertedWeather = new Weather($scale, $city, $date, $time, $value);

        return $convertedWeather;
    }

    /**
     * @param string $city
     * @param string $scale
     * @return Weather
     * @throws DomainInvalidRequestException
     */
    public function getToday(string $city, string $scale = ''): Weather
    {
        return $this->getByDate($city, date('Ymd'), date('H:00'), $scale);
    }

    /**
     * @param array $weather
     * @return string
     */
    protected function getKey(array $weather)
    {
        return sprintf("%s:%s:%s", $weather['city'], $weather['date'], $weather['time']);
    }
}