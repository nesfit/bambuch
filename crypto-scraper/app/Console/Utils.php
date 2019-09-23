<?php
/**
 * Project: BitInfoCharts parser
 * Author: Vladislav Bambuch - xbambu03@stud.fit.vutbr.cz
 */

namespace App\Console;

use DOMDocument;
use DOMXPath;
use ForceUTF8\Encoding;

class Utils {
    private const replaceRegexes = [
        '/wallet: /',
        '/-.*wallet$/',
        '/-.*\d$/',
        '/^Huobi$/',
        '/^Bittrex$/',
        '/^PoloniEx$/',
        '/^Poloniex$/',
    ];

    private const replaceValues = [
        '',
        '',
        '',
        'Huobi.com',
        'Bittrex.com',
        'Poloniex',
        'Poloniex.com',
    ];

    /**
     * Uses curl to get DOM from a url.
     *
     * @param string $url
     * @return string
     */
    public static function getContentFromURL($url) {
        sleep(1);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:21.0) Gecko/20100101 Firefox/21.0");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

        $data = curl_exec($curl);
        
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        return ($httpcode >= 200 && $httpcode < 300) ? $data : "";
    }

    /**
     * Turns plain DOM string into DOMXPath.
     *
     * @param string $data Plain DOM string
     * @return DOMXPath DOMXPath instance
     */
    public static function getDOMXPath($data) {
        $doc = new DOMDocument();
        $doc->loadHTML($data);
        return new DOMXPath($doc);
    }

    /**
     * Strips unwanted characters from owner label.
     *
     * @param string $label Owner label to be parsed
     * @return string Owner name
     */
    public static function getOwnerName($label) {
        return preg_replace(self::replaceRegexes, self::replaceValues, $label);
    }

    /**
     * Helper constructor for wallet.
     *
     * @param string $url
     * @param string $label
     * @param array $addresses
     * @return array
     */
    public static function newWallet($url, $label, $addresses) {
        return [
            "url" => $url,
            "label" => $label,
            "addresses" => $addresses
        ];
    }
    
    public static function getCryptoSettings(string $url) {
        switch (true) {
            case preg_match('/' . CryptoCurrency::BTC['name'] . '/' , $url): return CryptoCurrency::BTC;
            case preg_match('/' . CryptoCurrency::LTC['name']. '/', $url): return CryptoCurrency::LTC;
            case preg_match('/' . CryptoCurrency::BCH['name']. '/', $url): return CryptoCurrency::BCH;
            case preg_match('/' . CryptoCurrency::DASH['name']. '/', $url): return CryptoCurrency::DASH;
            case preg_match('/' . CryptoCurrency::BTG['name']. '/', $url): return CryptoCurrency::BTG;
        }
        return CryptoCurrency::EMPTY;
    }

    public static function createTSVData($owner, $url, $label, $source, $address, $cryptoType) {
        $cleanArray = array_reduce([$owner, $url, $label, $source, $address, $cryptoType],
            function ($acc, $value)  {
                $correctUtf = Encoding::toUTF8($value);
                array_push($acc, str_replace("\t", " ", $correctUtf));
                return $acc;
            }, []
        );
        return implode("\t", $cleanArray);
    }
    
    public static function getFullHost(string $url): string {
        $parsedUrl = parse_url($url);
        return $parsedUrl["scheme"] . "://" . $parsedUrl["host"];
    }
}
   