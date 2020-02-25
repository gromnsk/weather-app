<?php
declare(strict_types=1);

namespace App\Domain\Weather;

use App\Domain\DomainException\DomainRecordNotFoundException;

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
            $value += Scale::convert(strtolower($weather->getScale()), strtolower($scale), $weather->getValue());
        }

        if (!$weather) {
            throw new DomainRecordNotFoundException();
        }

        $resultWeather = new Weather($scale, $city, $weather->getDate(), $weather->getTime(), $value/count($this->getRepositories()));

        return $resultWeather;
    }

}
