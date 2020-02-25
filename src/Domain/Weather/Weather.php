<?php
declare(strict_types=1);

namespace App\Domain\Weather;

use JsonSerializable;

class Weather implements JsonSerializable
{
    /**
     * @var string
     */
    private $scale;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $date;

    /**
     * @var string
     */
    private $time;

    /**
     * @var int
     */
    private $value;

    /**
     * @param string $scale
     * @param string $city
     * @param string $date
     * @param string $time
     * @param int $value
     */
    public function __construct(string $scale, string $city, string $date, string $time, int $value)
    {
        $this->scale = $scale;
        $this->city = $city;
        $this->date = $date;
        $this->time = $time;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getScale(): string
    {
        return $this->scale;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getTime(): string
    {
        return $this->time;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'scale' => $this->scale,
            'city' => $this->city,
            'date' => $this->date,
            'time' => $this->time,
            'value' => $this->value,
        ];
    }
}
