<?php
/**
 * Created by PhpStorm.
 * User: linkuha (Pudich Aleksandr)
 * Date: 25.09.2020
 */

namespace CurrencyCalculator;

class AverageRateCalculator
{
    private $currenciesFrom = ['USD', 'EUR'];

    private $defaultLoaders = [
        RbkRatesLoader::class,
        CbrfRatesLoader::class
    ];

    /** @var RatesLoader[] */
    private $loaderInstances = [];

    public function __construct($loaders = [])
    {
        if (! empty($loaders)) {
            foreach ($loaders as $instance) {
                $this->addLoader($instance);
            }
        } else {
            foreach ($this->defaultLoaders as $class) {
                $instance = new $class();
                $this->addLoader($instance);
            }
        }
    }

    public function addLoader($instance)
    {
        $class = get_class($instance);
        if (! $instance instanceof RatesLoader) {
            throw new \InvalidArgumentException('Loader ' . $class . ' is not implement RatesLoader');
        }
        $this->loaderInstances[$class] = $instance;
    }

    /**
     * @param \DateTimeImmutable $date
     *
     * @return DailyRateResult[]
     */
    public function calc($date)
    {
        if (! ($date instanceof \DateTimeImmutable)) {
            throw new \InvalidArgumentException('Invalid date');
        }
        $result = [
            'date' => $date->format('Y-m-d')
        ];
        $ratesForCurrency = [];
        foreach ($this->loaderInstances as $loader) {
            foreach ($this->currenciesFrom as $currencyFrom) {
                $dailyRate = $loader->getDaily($date, $currencyFrom);

                $ratesForCurrency[$currencyFrom][] = $dailyRate->getRate();
            }
        }

        $averageRates = [];
        foreach ($ratesForCurrency as $currency => $rates) {
            $tmpCount = count($rates);
            $tmpSum = array_sum($rates);
            $averageRates[$currency] = $tmpSum / $tmpCount;
        }
        $result['average_rates'] = $averageRates;

        return $result;
    }

}