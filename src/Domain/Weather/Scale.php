<?php

declare(strict_types=1);

namespace App\Domain\Weather;

use App\Domain\DomainException\DomainInvalidRequestException;

class Scale
{
    const FAHRENHEIT_SCALE = 'fahrenheit';
    const CELSIUS_SCALE = 'celsius';

    /**
     * @param string $inputScale
     * @param string $outputScale
     * @param int $value
     * @return int
     * @throws DomainInvalidRequestException
     */
    public static function convert(string $inputScale, string $outputScale, int $value): int
    {
        $ret = null;
        switch ($inputScale) {
            case self::CELSIUS_SCALE:
                switch ($outputScale) {
                    case self::FAHRENHEIT_SCALE:
                        return (int)(($value * 9 / 5) + 32);
                    case self::CELSIUS_SCALE:
                        return $value;
                }
                break;
            case self::FAHRENHEIT_SCALE:
                switch ($outputScale) {
                    case self::CELSIUS_SCALE:
                        return (int)(($value - 32) / 1.8);
                    case self::FAHRENHEIT_SCALE:
                        return $value;
                }
                break;
            default:
                throw new DomainInvalidRequestException(sprintf("can't convert %s into %s", $inputScale, $outputScale));
        }
    }
}