<?php
/**
 * Created by PhpStorm.
 * User: linkuha (Pudich Aleksandr)
 * Date: 26.09.2020
 */

namespace CurrencyCalculator\Utils;

class JsonParser
{
    public static function parse($json)
    {
        if (empty($json)) {
            return null;
        }
        $jsonPayload = \json_decode($json);
        if ($errno = \json_last_error()) {
            static::handleJsonError($errno, json_last_error_msg());
        }
        return $jsonPayload;
    }

    private static function handleJsonError($errno, $msg)
    {
        $messages = array(
            JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH => 'Invalid or malformed JSON',
            JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
            JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON',
            JSON_ERROR_UTF8 => 'Malformed UTF-8 characters' //PHP >= 5.3.3
        );
        throw new \DomainException(
            isset($messages[$errno])
                ? $messages[$errno]
                : "Unknown JSON error: $errno ($msg)"
        );
    }
}