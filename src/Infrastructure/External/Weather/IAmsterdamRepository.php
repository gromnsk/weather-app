<?php
declare(strict_types=1);

namespace App\Infrastructure\External\Weather;

use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Domain\Weather\Weather;
use App\Domain\Weather\WeatherRepository;

class IAmsterdamRepository implements WeatherRepository
{
    protected $filePath;

    /**
     * IAmsterdamRepository constructor.
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
    public function getByDate(string $city, string $date, string $time, string $scale = ''): Weather
    {
        foreach ($this->getData()['predictions'] as $data) {
            if (strtolower($data['city']) === strtolower($city) && $data['date'] === $date) {
                foreach ($data['prediction'] as $prediction) {
                    if ($prediction['time'] === $time) {
                        return new Weather($data['-scale'], $city, $date, $time, $prediction['value']);
                    }
                }
            }
        }

        throw new DomainRecordNotFoundException(sprintf('weather for %s not found', $city));
    }

    protected function getData(): array
    {
        $data = file_get_contents($this->filePath);

        return json_decode($data, true);
    }
}