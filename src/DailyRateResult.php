<?php
/**
 * Created by PhpStorm.
 * User: linkuha (Pudich Aleksandr)
 * Date: 25.09.2020
 */

namespace CurrencyCalculator;

class DailyRateResult
{
    /**
     * @var \DateTimeImmutable
     */
    private $date = null;

    private $currencyFrom = '';

    private $currencyTo = '';

    private $rate = 1;

    public function __construct($date, $currencyFrom, $currencyTo, $rate)
    {
        if (! ($date instanceof \DateTimeImmutable)) {
            throw new \InvalidArgumentException('Invalid date');
        }
        if (! is_string($currencyFrom) || empty($currencyFrom)) {
            throw new \InvalidArgumentException('Invalid currencyFrom');
        }
        if (! is_string($currencyTo) || empty($currencyTo)) {
            throw new \InvalidArgumentException('Invalid currencyTo');
        }
        if (! is_numeric($rate) || ($rate = floatval($rate)) <= 0) {
            throw new \InvalidArgumentException('Invalid rate');
        }
        $this->date = $date;
        $this->currencyFrom = $currencyFrom;
        $this->currencyTo = $currencyTo;
        $this->rate = $rate;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getCurrencyFrom(): string
    {
        return $this->currencyFrom;
    }

    /**
     * @return string
     */
    public function getCurrencyTo(): string
    {
        return $this->currencyTo;
    }

    /**
     * @return float|int
     */
    public function getRate()
    {
        return $this->rate;
    }

}