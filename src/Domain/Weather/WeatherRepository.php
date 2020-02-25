<?php
declare(strict_types=1);

namespace App\Domain\Weather;

interface WeatherRepository
{
    /**
     * @param string $city
     * @param string $scale
     * @return Weather
     */
    public function getToday(string $city, string $scale = ''): Weather;

    /**
     * @param string $city
     * @param string $date
     * @param string $time
     * @param string $scale
     * @return Weather
     */
    public function getByDate(string $city, string $date, string $time, string $scale = ''): Weather;
}
