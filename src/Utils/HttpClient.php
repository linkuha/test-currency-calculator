<?php
/**
 * Created by PhpStorm.
 * User: linkuha (Pudich Aleksandr)
 * Date: 26.09.2020
 */

namespace CurrencyCalculator\Utils;


use CurrencyCalculator\Exceptions\ServiceUnavailableException;

class HttpClient
{
    public static function sendGetRequest($url, $params = [], $allowedFormat = '')
    {
        $url = "$url?" . http_build_query($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $response = curl_exec($ch);

        $responseInfo = [];
        if (! curl_errno($ch)) {
            $responseInfo = curl_getinfo($ch);
            curl_close($ch);
        } else {
            $error = curl_error($ch);
            curl_close($ch);
            throw new ServiceUnavailableException("Connection error: $error");
        }

        if (empty($response)) {
            return null;
//            throw new ServiceUnavailableException('Url return empty response');
        }

        $httpCode = $responseInfo['http_code'] ?? 0;
        if (! ($httpCode >= 200 && $httpCode < 300)) {
            return null;
//            throw new ServiceUnavailableException('Url return not success code: ' . $httpCode);
        }

        if (! empty($allowedFormat)) {
            $contentType = $responseInfo['content_type'] ?? '';
            if (false === strpos($contentType, $allowedFormat)) {
                return null;
//                throw new ServiceUnavailableException('Url return not valid format: ' . $contentType);
            }
        }

        return $response;
    }
}