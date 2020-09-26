<?php
/**
 * Created by PhpStorm.
 * User: linkuha (Pudich Aleksandr)
 * Date: 25.09.2020
 */

namespace CurrencyCalculator;

use CurrencyCalculator\Utils\HttpClient;
use CurrencyCalculator\Utils\XmlParser;

class CbrfRatesLoader implements RatesLoader
{
    private $apiBaseUrl = 'http://www.cbr.ru/scripts/';

    private $dailyRates = [];

    public function getDaily($date, $currencyFrom): ?DailyRateResult
    {
        if (! ($date instanceof \DateTimeImmutable)) {
            throw new \InvalidArgumentException('Invalid date');
        }
        if (! is_string($currencyFrom) || empty($currencyFrom)) {
            throw new \InvalidArgumentException('Invalid currencyFrom');
        }
        $dailyRates = $this->getCurrenciesRates($date);
        if (empty($dailyRates)) {
            return null;
        }
        foreach ($dailyRates as $dailyRate) {
            if ($currencyFrom === $dailyRate->getCurrencyFrom()) {
                return $dailyRate;
            }
        }
        return null;
    }

    /**
     * @param \DateTimeImmutable $date
     * @return DailyRateResult[]
     * @throws Exceptions\ServiceUnavailableException
     */
    private function getCurrenciesRates($date)
    {
        $dateFormatted = $date->format('d/m/Y');
        if (! empty($this->dailyRates[$dateFormatted])) {
            return $this->dailyRates[$dateFormatted];
        }
        $url = $this->apiBaseUrl . 'XML_daily.asp';
        $response =  HttpClient::sendGetRequest($url, [
            'date_req' => $dateFormatted
        ], 'application/xml');
        $result = $this->processResponse($date, $response);

        return ($this->dailyRates[$dateFormatted] = $result);
    }

    /**
     * @param $date
     * @param $response
     * @return DailyRateResult[]
     */
    private function processResponse($date, $response)
    {
        $xmlObj = XmlParser::parse($response);
        if (! $xmlObj || $xmlObj->getName() !== 'ValCurs') {
            return null;
        }
        $dailyRates = [];

        if (isset($xmlObj->Valute)) {
            $currencies = $xmlObj->Valute;
            foreach ($currencies as $currency) {
                $currencyFrom = (string)$currency->CharCode;
                $value = str_replace(',', '.', (string)$currency->Value);
                $nominal = str_replace(',', '.', (string)$currency->Nominal);
                $rate =(float)$value / (float)$nominal;

                $dailyRates[] = new DailyRateResult($date, $currencyFrom, 'RUR', $rate);
            }
        }

        return $dailyRates;
    }

}