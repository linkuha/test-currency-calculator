<?php
/**
 * Created by PhpStorm.
 * User: linkuha (Pudich Aleksandr)
 * Date: 25.09.2020
 */

use CurrencyCalculator\DailyRateResult;

class DailyRateResultTest extends PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider invalidParamsProvider
     */
    public function testInvalidParams($date, $currencyFrom, $currencyTo, $rate)
    {
        $this->expectException(InvalidArgumentException::class);

        new DailyRateResult($date, $currencyFrom, $currencyTo, $rate);
    }

    public function invalidParamsProvider()
    {
        return [
            [null, 'EUR', 'RUR', 1],
            [new \DateTimeImmutable(), '', 'RUR', 1],
            [new \DateTimeImmutable(), 'USD', '', 1],
            [new \DateTimeImmutable(), null, 'RUR', 1],
            [new \DateTimeImmutable(), 'EUR', null, 1],
            [new \DateTimeImmutable(), 'EUR', 'RUR', null],
            [new \DateTimeImmutable(), 'EUR', 'RUR', -1],
            [new \DateTimeImmutable(), 'EUR', 'RUR', 0],
        ];
    }

    /**
     * @dataProvider validParamsProvider
     */
    public function testValidParams($date, $currencyFrom, $currencyTo, $rate)
    {
        $dailyRateObject = new DailyRateResult($date, $currencyFrom, $currencyTo, $rate);
        $this->assertSame($date, $dailyRateObject->getDate());
        $this->assertSame($currencyFrom, $dailyRateObject->getCurrencyFrom());
        $this->assertSame($currencyTo, $dailyRateObject->getCurrencyTo());
        $this->assertSame((float) $rate, $dailyRateObject->getRate());
    }

    public function validParamsProvider()
    {
        return [
            [new \DateTimeImmutable(), 'EUR', 'RUR', 134],
            [new \DateTimeImmutable(), 'EUR', 'RUR', 75.123],
            [new \DateTimeImmutable(), 'EUR', 'RUR', '0.31'],
        ];
    }
}