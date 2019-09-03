<?php
/**
 * Project: BitInfoCharts parser
 * Author: Vladislav Bambuch - xbambu03@stud.fit.vutbr.cz
 */

namespace App\Console;

class Config {
    private const MAIN_URL = "https://bitinfocharts.com";
    private const PHOLDER = "__placeholder__";

    const BTC   = ['name' => 'bitcoin', 'code' => 1, 'regex' => '/([13][a-km-zA-HJ-NP-Z1-9]{25,34})/'];
    const LTC   = ['name' => 'litecoin', 'code' => 2, 'regex' => '/([LM3][a-km-zA-HJ-NP-Z1-9]{25,34})/'];
    const DASH  = ['name' => 'dash', 'code' => 3, 'regex' => '/(X[1-9A-HJ-NP-Za-km-z]{33})/'];
    const ZEC   = ['name' => 'zcash', 'code' => 4, 'regex' => ''];
    const BCH   = ['name' => 'bitcoin cash', 'code' => 5, 'regex' => '/([13][a-km-zA-HJ-NP-Z1-9]{25,34})/'];
    const DGB   = ['name' => 'digibyte', 'code' => 6, 'regex' => ''];
    const BTG   = ['name' => 'bitcoin gold', 'code' => 7, 'regex' => '/([AG][a-km-zA-HJ-NP-Z1-9]{25,34})/'];
    const QTUM  = ['name' => 'qtum', 'code' => 8, 'regex' => ''];
    const ETH   = ['name' => 'ethereum', 'code' => 10, 'regex' => ''];
    const EMPTY   = ['name' => 'empty', 'code' => -1, 'regex' => 'empty'];

    /**
     * Concatenates main url with input link.
     *
     * @param string $link
     * @return string Full url
     */
    public static function getUrl($link) {
        return self::MAIN_URL . $link;
    }

    /**
     * Gets main source.
     *
     * @return string Main source/url
     */
    public static function getSource() {
        return self::MAIN_URL;
    }

    /**
     * Concatenates main url with input path and index.
     * Puts the index to correct possition in the path using placeholder.
     *
     * @param string $path
     * @param int $index
     * @return string Full page url
     */
    public static function createPageUrl(string $path, int $index) {
        return self::MAIN_URL . str_replace(self::PHOLDER, $index, $path) . ".html";
    }

    public static function getCryptoByName(string $name) {
        switch ($name) {
            case self::BTC['name']: return self::BTC;
            case self::LTC['name']: return self::LTC;
            case self::BCH['name']: return self::BCH;
            case self::DASH['name']: return self::DASH;
            case self::BTG['name']: return self::BTG;
        }
    }
    
    /**
     * Gets configs with cryptocurrencies and assigned urls.
     *
     * @return array
     */
    public static function getPages() {
        return [
            ["path" => "/top-100-richest-bitcoin-addresses-".self::PHOLDER, "maxPage" => 100],
            ["path" => "/top-100-busiest_by_sum-bitcoin-addresses-".self::PHOLDER, "maxPage" => 21],
            ["path" => "/top-100-busiest_by_transactions-bitcoin-addresses-".self::PHOLDER, "maxPage" => 21],
            ["path" => "/top-100-dormant_".self::PHOLDER."y-bitcoin-addresses", "maxPage" => 9],
            ["path" => "/top-100-richest-litecoin-addresses-".self::PHOLDER, "maxPage" => 100],
            ["path" => "/top-100-busiest_by_sum-litecoin-addresses-".self::PHOLDER, "maxPage" => 21],
            ["path" => "/top-100-busiest_by_transactions-litecoin-addresses-".self::PHOLDER, "maxPage" => 21],
            ["path" => "/top-100-dormant_".self::PHOLDER."y-litecoin-addresses", "maxPage" => 9],
            ["path" => "/top-100-richest-bitcoin%20gold-addresses-".self::PHOLDER, "maxPage" => 100],
            ["path" => "/top-100-busiest_by_sum-bitcoin%20gold-addresses-".self::PHOLDER, "maxPage" => 21],
            ["path" => "/top-100-busiest_by_transactions-bitcoin%20gold-addresses-".self::PHOLDER, "maxPage" => 21],
            ["path" => "/top-100-dormant_".self::PHOLDER."y-bitcoin%20gold-addresses", "maxPage" => 9],
            ["path" => "/top-100-richest-bitcoin%20cash-addresses-".self::PHOLDER, "maxPage" => 100],
            ["path" => "/top-100-busiest_by_sum-bitcoin%20cash-addresses-".self::PHOLDER, "maxPage" => 21],
            ["path" => "/top-100-busiest_by_transactions-bitcoin%20cash-addresses-".self::PHOLDER, "maxPage" => 21],
            ["path" => "/top-100-dormant_".self::PHOLDER."y-bitcoin%20cash-addresses", "maxPage" => 9],
            ["path" => "/top-100-richest-dash-addresses-".self::PHOLDER, "maxPage" => 100],
            ["path" => "/top-100-busiest_by_sum-dash-addresses-".self::PHOLDER, "maxPage" => 21],
            ["path" => "/top-100-busiest_by_transactions-dash-addresses-".self::PHOLDER, "maxPage" => 21],
            ["path" => "/top-100-dormant_".self::PHOLDER."y-dash-addresses", "maxPage" => 9]
        ];
    }
}