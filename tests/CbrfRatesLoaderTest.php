<?php
/**
 * Created by PhpStorm.
 * User: linkuha (Pudich Aleksandr)
 * Date: 26.09.2020
 */

use CurrencyCalculator\CbrfRatesLoader;

class CbrfRatesLoaderTest extends PHPUnit\Framework\TestCase
{
    public function testGettingDailyRate()
    {
        $loader = new CbrfRatesLoader();
        $date = new DateTimeImmutable('2020-09-19');
        $dailyRate = $loader->getDaily($date, 'EUR');

        $this->assertSame($date, $dailyRate->getDate());
        $this->assertSame('EUR', $dailyRate->getCurrencyFrom());
        $this->assertSame('RUR', $dailyRate->getCurrencyTo());
        $this->assertIsFloat($dailyRate->getRate());
    }

    public function testInvalidDate()
    {
        $this->expectException(InvalidArgumentException::class);

        $loader = new CbrfRatesLoader();
        $date = null;
        $dailyRate = $loader->getDaily($date, 'EUR');
    }

    public function testInvalidCurrency()
    {
        $this->expectException(InvalidArgumentException::class);

        $loader = new CbrfRatesLoader();
        $date = new DateTimeImmutable('2020-09-19');
        $dailyRate = $loader->getDaily($date, '');
    }

    public function testUnknownCurrency()
    {
        $loader = new CbrfRatesLoader();
        $date = new DateTimeImmutable('2020-09-19');
        $dailyRate = $loader->getDaily($date, 'unknown');

        $this->assertEmpty($dailyRate);
    }

    public function testErrorContent()
    {
        $loader = new CbrfRatesLoader();

        $this->setPrivateField($loader, 'apiBaseUrl', 'http://example.com/');

        $date = new DateTimeImmutable('2020-09-19');
        $dailyRate = $loader->getDaily($date, 'USD');

        $this->assertNull($dailyRate);
    }

    public function testLoadTwice()
    {
        $loader = new CbrfRatesLoader();
        $date = new DateTimeImmutable('2020-09-19');
        $dailyRate1 = $loader->getDaily($date, 'EUR');

        $this->assertSame($date, $dailyRate1->getDate());
        $this->assertSame('EUR', $dailyRate1->getCurrencyFrom());
        $this->assertSame('RUR', $dailyRate1->getCurrencyTo());
        $this->assertIsFloat($dailyRate1->getRate());

        $dailyRate2 = $loader->getDaily($date, 'EUR');

        $this->assertSame($date, $dailyRate2->getDate());
        $this->assertSame('EUR', $dailyRate2->getCurrencyFrom());
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