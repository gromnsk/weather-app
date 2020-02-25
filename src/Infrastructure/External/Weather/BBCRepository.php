<?php
declare(strict_types=1);

namespace App\Infrastructure\External\Weather;

use App\Domain\DomainException\DomainRecordNotFoundException;
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
     */
    public function getByDate(string $city, string $date, string $time, string $scale =''): Weather
    {
        foreach ($this->getData()->prediction as $data) {
            if (strtolower($data->city->__toString()) === strtolower($city) && $data->date->__toString() === $date) {
                foreach ($data->prediction as $prediction) {
                    if ($prediction->time->__toString() === $time) {
                        return new Weather((string)$data->attributes()->scale, $city, $date, $time, (int)$prediction->value->__toString());
                    }
                }
            }
        }

        throw new DomainRecordNotFoundException(sprintf('weather for %s not found', $city));
    }

    public function getData(): SimpleXMLElement
    {
        $data = file_get_contents($this->filePath);

        return new SimpleXMLElement($data);
    }
}