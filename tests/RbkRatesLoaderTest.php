<?php
/**
 * Created by PhpStorm.
 * User: linkuha (Pudich Aleksandr)
 * Date: 26.09.2020
 */

use CurrencyCalculator\Exceptions\ServiceUnavailableException;
use CurrencyCalculator\RbkRatesLoader;

class RbkRatesLoaderTest extends PHPUnit\Framework\TestCase
{
    public function testGettingDailyRate()
    {
        $loader = new RbkRatesLoader();
        $date = new DateTimeImmutable('2020-09-19');
        $currency = 'USD';
        $dailyRate = $loader->getDaily($date, $currency);

        $this->assertSame($date, $dailyRate->getDate());
        $this->assertSame($currency, $dailyRate->getCurrencyFrom());
        $this->assertSame('RUR', $dailyRate->getCurrencyTo());
        $this->assertIsFloat($dailyRate->getRate());
    }

    public function testInvalidDate()
    {
        $this->expectException(InvalidArgumentException::class);

        $loader = new RbkRatesLoader();
        $date = null;
        $dailyRate = $loader->getDaily($date, 'EUR');
    }

    public function testInvalidCurrency()
    {
        $this->expectException(InvalidArgumentException::class);

        $loader = new RbkRatesLoader();
        $date = new DateTimeImmutable('2020-09-19');
        $dailyRate = $loader->getDaily($date, '');
    }

    public function testUnknownCurrency()
    {
        $loader = new RbkRatesLoader();
        $date = new DateTimeImmutable('2020-09-19');
        $dailyRate = $loader->getDaily($date, 'unknown');

        $this->assertEmpty($dailyRate);
    }

    public function testErrorContent()
    {
        $loader = new RbkRatesLoader();

        $this->setPrivateField($loader, 'apiBaseUrl', 'http://example.com/');

        $date = new DateTimeImmutable('2020-09-19');
        $dailyRate = $loader->getDaily($date, 'USD');

        $this->assertNull($dailyRate);
    }

    public function testLoadTwice()
    {
        $loader = new RbkRatesLoader();
        $date = new DateTimeImmutable('2020-09-19');
        $currency = 'USD';
        $dailyRate1 = $loader->getDaily($date, $currency);

        $this->assertSame($date, $dailyRate1->getDate());
        $this->assertSame($currency, $dailyRate1->getCurrencyFrom());
        $this->assertSame('RUR', $dailyRate1->getCurrencyTo());
        $this->assertIsFloat($dailyRate1->getRate());

        $dailyRate2 = $loader->getDaily($date, $currency);

        $this->assertSame($date, $dailyRate2->getDate());
        $this->assertSame($currency, $dailyRate2->getCurrencyFrom());
        $this->assertSame('RUR', $dailyRate2->getCurrencyTo());
        $this->assertIsFloat($dailyRate2->getRate());
    }

    private function setPrivateField($object, $fieldName, $fieldValue)
    {
        $className = get_class($object);
        $refl = new \ReflectionClass($className);
        $property = $refl->getProperty($fieldName);
        $property->setAccessible(true);
        $property->setValue($object, $fieldValue);
    }
}