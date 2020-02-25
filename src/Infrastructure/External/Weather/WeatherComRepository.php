<?php
declare(strict_types=1);

namespace App\Infrastructure\External\Weather;

use App\Domain\DomainException\DomainInvalidRequestException;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Domain\Weather\Scale;
use App\Domain\Weather\Weather;
use App\Domain\Weather\WeatherRepository;

class WeatherComRepository implements WeatherRepository
{
    const DEFAULT_SCALE = Scale::CELSIUS_SCALE;

    const CSV_SCALE = 0;
    const CSV_CITY = 1;
    const CSV_DATE = 2;
    const CSV_TIME = 3;
    const CSV_VALUE = 4;

    /** @var string */
    protected $filePath;

    /**
     * WeatherComRepository constructor.
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
        foreach ($this->getData() as $data) {
            if (
                strtolower($data[self::CSV_CITY]) === strtolower($city)
                && $data[self::CSV_DATE] === $date
                && $data[self::CSV_TIME] === $time
            ) {
                $value = Scale::convert(
                    strtolower($data[self::CSV_SCALE]),
                    Scale::CELSIUS_SCALE,
                    (int)$data[self::CSV_VALUE]
                );
                return new Weather(Scale::CELSIUS_SCALE, $city, $date, $time, $value);
            }
        }

        throw new DomainRecordNotFoundException(sprintf('weather for %s not found', $city));
    }

    protected function getData(): array
    {
        $ret = [];
        if (($handle = fopen($this->filePath, "r")) !== false) {
            while (($data = fgetcsv($handle, 0, ",")) !== false) {
                $ret[] = $data;
            }
            fclose($handle);
        }

        return $ret;
    }
}