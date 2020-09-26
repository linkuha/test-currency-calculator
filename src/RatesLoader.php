<?php
/**
 * Created by PhpStorm.
 * User: linkuha (Pudich Aleksandr)
 * Date: 25.09.2020
 */

namespace CurrencyCalculator;

interface RatesLoader
{
    public function getDaily($date, $currencyFrom): ?DailyRateResult;

//    public function getDailyCached($date, $currencyFrom, $currencyTo = 'RUR');
}