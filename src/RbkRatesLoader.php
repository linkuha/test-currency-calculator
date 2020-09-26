<?php
/**
 * Created by PhpStorm.
 * User: linkuha (Pudich Aleksandr)
 * Date: 25.09.2020
 */

namespace CurrencyCalculator;

use CurrencyCalculator\Utils\HttpClient;
use CurrencyCalculator\Utils\JsonParser;

class RbkRatesLoader implements RatesLoader
{
    private $apiBaseUrl = 'https://cash.rbc.ru/cash/json/converter_currency_rate/';

    /**
     * @var DailyRateResult[]
     */
    private $dailyRates = [];

    public function getDaily($date, $currencyFrom): ?DailyRateResult
    {
        if (! ($date instanceof \DateTimeImmutable)) {
            throw new \InvalidArgumentException('Invalid date');
        }
        if (! is_string($currencyFrom) || empty($currencyFrom)) {
            throw new \InvalidArgumentException('Invalid currencyFrom');
        }
        $dateFormatted = $date->format('Y-m-d');
        $key = $dateFormatted . '_' . $currencyFrom;
        if (! empty($this->dailyRates[$key])) {
            return $this->dailyRates[$key];
        }

        $response = HttpClient::sendGetRequest($this->apiBaseUrl, [
            'currency_from' => $currencyFrom,
            'currency_to' => 'RUR',
            'source' => 'cbrf',
            'sum' => 1,
            'date' => $dateFormatted
        ], 'application/json');
        $result = $this->processResponse($date, $response);

        return ($this->dailyRates[$key] = $result);
    }

    /**
     * @param $date
     * @param $response
     * @return DailyRateResult
     */
    private function processResponse($date, $response)
    {
        $jsonObj = JsonParser::parse($response);
        if (! $jsonObj || $jsonObj->status !== 200 || empty($jsonObj->data) || empty($jsonObj->meta)) {
            return null;
        }
        $currencyFrom = $jsonObj->meta->currency_from;
        $rate = $jsonObj->data->sum_result;
        return new DailyRateResult($date, $currencyFrom, 'RUR', $rate);
    }


}