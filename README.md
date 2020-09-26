## Test lib - average currency (rubles) rate calculator 

Using API: https://www.cbr.ru/development/SXML/ and https://cash.rbc.ru/cash/json/converter_currency_rate/?currency_from=EUR&currency_to=RUR&source=cbrf&sum=1&date=2020-09-21 PS. Also uou can add own loader.

Currencies setup: USD, EUR

INSTALLATION
------------

### Install

``git clone https://github.com/linkuha/test-currency-calculator.git``

### Usage

```
$calc = new AverageRateCalculator();
$date = new DateTimeImmutable('2020-09-19');
$result = $calc->calc($date);
```
output array:
```
[
    'date' => '2020-09-19',
    'average_rates' => [
        'USD' => 75.0319,
        'EUR' => 88.9578
    ],
]
```

Custom API loaders:
```
$rbkLoader = new RbkRatesLoader(); // implements RatesLoader interface
$cbrfLoader = new CbrfRatesLoader();

$calc = new AverageRateCalculator([$rbkLoader]); // in construct
$calc->addLoader($cbrfLoader); // method

$date = new DateTimeImmutable('2020-09-19');
$result = $calc->calc($date);
```

### Tests

You can find commands in makefile
```
# all
make tests-run

# generate html coverage
make tests-run-coverage

# other
make tests-run-cbrf
make tests-run-rbk
make tests-run-calc
```
