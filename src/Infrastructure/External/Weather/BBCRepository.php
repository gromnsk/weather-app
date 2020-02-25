<?php
declare(strict_types=1);

namespace App\Infrastructure\External\Weather;

use App\Domain\DomainException\DomainInvalidRequestException;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Domain\Weather\Scale;
use App\Domain\Weather\Weather;
use App\Domain\Weather\WeatherRepository;
use SimpleXMLElement;

class BBCRepository implements WeatherRepository
{
    protected $filePath;

    /**
     * BBCRepository constructor.
     * @param string $filePath
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @param string $city
     * @param string $scale
     * @return Weather
     * @throws DomainRecordNotFoundException
     */
    public function getToday(string $city, string $scale = ''): Weather
    {
        return $this->getByDate($city, date('Ymd'), date('H:00'));
    }

    /**
     * @param string $city
     * @param string $date
     * @param string $time
     * @param string $scale
     * @return Weather
     * @throws DomainRecordNotFoundException
     * @throws DomainInvalidRequestException
     */
    public function getByDate(string $city, string $date, string $time, string $scale = ''): Weather
    {
        foreach ($this->getData()->prediction as $data) {
            if (strtolower($data->city->__toString()) === strtolower($city) && $data->date->__toString() === $date) {
                foreach ($data->prediction as $prediction) {
                    if ($prediction->time->__toString() === $time) {
                        $value = Scale::convert(
                            strtolower((string)$data->attributes()->scale),
                            Scale::CELSIUS_SCALE,
                            (int)$prediction->value->__toString()
                        );
                        return new Weather(Scale::CELSIUS_SCALE, $city, $date, $time, $value);
                    }
                }
            }
        }

        throw new DomainRecordNotFoundException(sprintf('weather for %s not found', $city));
    }

    protected function getData(): SimpleXMLElement
    {
        $data = file_get_contents($this->filePath);

        return new SimpleXMLElement($data);
    }
}