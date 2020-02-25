<?php
declare(strict_types=1);

namespace App\Domain\Weather;

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
     */
    public function getByDate(string $city, string $date, string $time, string $scale = ''): Weather
    {
        $cachedValue = $this->cachierClient->get($this->getKey([
            'city' => $city,
            'date' => $date,
            'time' => $time,
            'scale' => $scale,
        ]));
        if (!empty($cachedValue)) {
            return new Weather($scale, $city, $date, $time, (int)$cachedValue);
        }
        $weather = $this->repository->getByDate($city, $date, $time, $scale);
        $key = $this->getKey($weather->jsonSerialize());
        $this->cachierClient->set($key, $weather->getValue());
        $this->cachierClient->expire($key, $this->ttl);

        return $weather;
    }

    /**
     * @param string $city
     * @param string $scale
     * @return Weather
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
        return sprintf("%s:%s:%s:%s", $weather['city'], $weather['scale'], $weather['date'], $weather['time']);
    }
}