<?php
declare(strict_types=1);

namespace App\Application\Actions\Weather;

use App\Application\Actions\Action;
use App\Domain\Weather\WeatherRepositories;
use App\Domain\Weather\WeatherRepository;
use Psr\Log\LoggerInterface;

abstract class WeatherAction extends Action
{
    /**
     * @var WeatherRepository
     */
    protected $weatherRepository;

    /**
     * @param LoggerInterface $logger
     * @param WeatherRepository $weatherRepository
     */
    public function __construct(LoggerInterface $logger, WeatherRepository $weatherRepository)
    {
        parent::__construct($logger);
        $this->weatherRepository = $weatherRepository;
    }
}
