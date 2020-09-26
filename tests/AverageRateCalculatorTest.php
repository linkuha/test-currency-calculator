<?php
/**
 * Created by PhpStorm.
 * User: linkuha (Pudich Aleksandr)
 * Date: 25.09.2020
 */

use CurrencyCalculator\AverageRateCalculator;
use CurrencyCalculator\CbrfRatesLoader;
use CurrencyCalculator\Exceptions\ServiceUnavailableException;
use CurrencyCalculator\RbkRatesLoader;

class AverageRateCalculatorTest extends PHPUnit\Framework\TestCase
{
    public function testDefaultWorks()
    {
        $calc = new AverageRateCalculator();
        $date = new DateTimeImmutable('2020-09-19');
        $result = $calc->calc($date);

        $this->assertIsArray($result);
        $this->assertEquals(75.0319, $result['average_rates']['USD']);
        $this->assertEquals(88.9578, $result['average_rates']['EUR']);
    }

    public function testCustomWorks()
    {
        $rbkLoader = new RbkRatesLoader();
        $cbrfLoader = new CbrfRatesLoader();

        $calc = new AverageRateCalculator([$rbkLoader, $cbrfLoader]);
        $date = new DateTimeImmutable('2020-09-19');
        $result = $calc->calc($date);

        $this->assertIsArray($result);
        $this->assertEquals(75.0319, $result['average_rates']['USD']);
        $this->assertEquals(88.9578, $result['average_rates']['EUR']);
    }

    public function testAddCustomLoader()
    {
        $rbkLoader = new RbkRatesLoader();
        $cbrfLoader = new CbrfRatesLoader();

        $calc = new AverageRateCalculator([$rbkLoader]);
        $calc->addLoader($cbrfLoader);
        $date = new DateTimeImmutable('2020-09-19');
        $result = $calc->calc($date);

        $this->assertIsArray($result);
        $this->assertEquals(75.0319, $result['average_rates']['USD']);
        $this->assertEquals(88.9578, $result['average_rates']['EUR']);
    }

    public function testInvalidLoaderInConstruct()
    {
        $rbkLoader = new RbkRatesLoader();
        $cbrfLoader = new stdClass();

        $this->expectException(InvalidArgumentException::class);

        $calc = new AverageRateCalculator([$rbkLoader, $cbrfLoader]);
    }

    public function testInvalidDate()
    {
        $calc = new AverageRateCalculator();

        $this->expectException(InvalidArgumentException::class);

        $date = new stdClass();
        $result = $calc->calc($date);
    }

    public function testRbkUnavailable()
    {
        $rbkLoader = new RbkRatesLoader();
        $cbrfLoader = new CbrfRatesLoader();

        $this->setPrivateField($rbkLoader, 'apiBaseUrl', 'http://unavaliable/');

        $this->expectException(ServiceUnavailableException::class);

        $calc = new AverageRateCalculator([$rbkLoader, $cbrfLoader]);
        $date = new DateTimeImmutable('2020-09-19');
        $result = $calc->calc($date);
    }

    public function testCbrfUnavailable()
    {
        $rbkLoader = new RbkRatesLoader();
        $cbrfLoader = new CbrfRatesLoader();

        $this->setPrivateField($cbrfLoader, 'apiBaseUrl', 'http://unavaliable/');

        $this->expectException(ServiceUnavailableException::class);

        $calc = new AverageRateCalculator([$rbkLoader, $cbrfLoader]);
        $date = new DateTimeImmutable('2020-09-19');
        $result = $calc->calc($date);
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