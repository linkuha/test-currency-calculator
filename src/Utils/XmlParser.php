<?php
/**
 * Created by PhpStorm.
 * User: linkuha (Pudich Aleksandr)
 * Date: 26.09.2020
 */

namespace CurrencyCalculator\Utils;


class XmlParser
{
    /**
     * @param $xml
     * @return \SimpleXMLElement
     */
    public static function parse($xml)
    {
        if (empty($xml)) {
            return null;
        }
        $priorSetting = libxml_use_internal_errors(true);
        try {
            libxml_clear_errors();
            $xmlPayload = simplexml_load_string($xml);
            if ($error = libxml_get_last_error()) {
                throw new \RuntimeException($error->message);
            }
        } catch (\Exception $e) {
            throw new \RuntimeException("Error parsing XML: {$e->getMessage()}");
        } finally {
            libxml_use_internal_errors($priorSetting);
        }

        return $xmlPayload;
    }
}