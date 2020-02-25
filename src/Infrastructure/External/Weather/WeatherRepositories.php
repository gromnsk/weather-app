<?php
declare(strict_types=1);

namespace App\Infrastructure\External\Weather;

use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Domain\Weather\Scale;
use App\Domain\Weather\Weather;
use App\Domain\Weather\WeatherRepository;

class WeatherRepositories implements WeatherRepository
{
    /** @var WeatherRepository[] */
    protected $repositories = [];

    public function add(WeatherRepository $repository)
    {
        $this->repositories[] = $repository;
    }

    /**
     * @return WeatherRepository[]|array
     */
    public function getRepositories()
    {
        return $this->repositories;
    }

    /**
     * @param string $city
     * @param string $scale
     * @return Weather
     * @throws DomainRecordNotFoundException
     * @throws \App\Domain\DomainException\DomainInvalidRequestException
     */
    public function getToday(string $city, string $scale = ''): Weather
    {
        return $this->getByDate($city, date('Ymd'), date('H:00'), $scale);
    }

    /**
     * @param string $city
     * @param string $date
     * @param string $time
     * @param string $scale
     * @return Weather
     * @throws DomainRecordNotFoundException
     * @throws \App\Domain\DomainException\DomainInvalidRequestException
     */
    public function getByDate(string $city, string $date, string $time, string $scale = ''): Weather
    {
        $value = 0;
        $weather = null;
        foreach ($this->getRepositories() as $repository) {
            $weather = $repository->getByDate($city, $date, $time, $scale);
            $value += $weather->getValue();
        }

        if (!$weather) {
            throw new DomainRecordNotFoundException();
        }

        $resultWeather = new Weather(
            Scale::CELSIUS_SCALE,
            $city,
            $weather->getDate(),
            $weather->getTime(),
            $value / count($this->getRepositories())
        );

        return $resultWeather;
    }

}
