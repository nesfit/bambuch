<?php
/**
 * Project: BitInfoCharts parser
 * Author: Vladislav Bambuch - xbambu03@stud.fit.vutbr.cz
 */

namespace App\Console;

class Config {
    private const MAIN_URL = "https://bitinfocharts.com";
    private const PHOLDER = "__placeholder__";

    const BTC   = 1;
    const LTC   = 2;
    const DASH  = 3;
    const ZEC   = 4;
    const BCH   = 5;
    const DGB   = 6;
    const BTG   = 7;
    const QTUM  = 8;
    const ETH   = 10;

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
     * @param string $link
     * @return string Full page url
     */
    public static function createPageUrl($path, $index) {
        return self::MAIN_URL . str_replace(self::PHOLDER, $index, $path) . ".html";
    }

    /**
     * Gets configs with cryptocurrencies and assigned urls.
     *
     * @return array
     */
    public static function getConfigs() {
        return [
            [  
                "cryptoType" => self::BTC,
                "cryptoRegex" => "/([13][a-km-zA-HJ-NP-Z1-9]{25,34})/",
                "pages" => [
                    ["path" => "/top-100-richest-bitcoin-addresses-".self::PHOLDER, "maxPage" => 100],
                    ["path" => "/top-100-busiest_by_sum-bitcoin-addresses-".self::PHOLDER, "maxPage" => 21],
                    ["path" => "/top-100-busiest_by_transactions-bitcoin-addresses-".self::PHOLDER, "maxPage" => 21],
                    ["path" => "/top-100-dormant_".self::PHOLDER."y-bitcoin-addresses", "maxPage" => 9],
                ]
            ],
            [  
                "cryptoType" => self::LTC,
                "cryptoRegex" => "/([LM3][a-km-zA-HJ-NP-Z1-9]{25,34})/",
                "pages" => [
                    ["path" => "/top-100-richest-litecoin-addresses-".self::PHOLDER, "maxPage" => 100],
                    ["path" => "/top-100-busiest_by_sum-litecoin-addresses-".self::PHOLDER, "maxPage" => 21],
                    ["path" => "/top-100-busiest_by_transactions-litecoin-addresses-".self::PHOLDER, "maxPage" => 21],
                    ["path" => "/top-100-dormant_".self::PHOLDER."y-litecoin-addresses", "maxPage" => 9],
                ]
            ],
            [
                "cryptoType" => self::BTG,
                "cryptoRegex" => "/([AG][a-km-zA-HJ-NP-Z1-9]{25,34})/",
                "pages" => [
                    ["path" => "/top-100-richest-bitcoin%20gold-addresses-".self::PHOLDER, "maxPage" => 100],
                    ["path" => "/top-100-busiest_by_sum-bitcoin%20gold-addresses-".self::PHOLDER, "maxPage" => 21],
                    ["path" => "/top-100-busiest_by_transactions-bitcoin%20gold-addresses-".self::PHOLDER, "maxPage" => 21],
                    ["path" => "/top-100-dormant_".self::PHOLDER."y-bitcoin%20gold-addresses", "maxPage" => 9],
                ]
            ],
            [
                "cryptoType" => self::BCH,
                "cryptoRegex" => "/([13][a-km-zA-HJ-NP-Z1-9]{25,34})/",
                "pages" => [
                    ["path" => "/top-100-richest-bitcoin%20cash-addresses-".self::PHOLDER, "maxPage" => 100],
                    ["path" => "/top-100-busiest_by_sum-bitcoin%20cash-addresses-".self::PHOLDER, "maxPage" => 21],
                    ["path" => "/top-100-busiest_by_transactions-bitcoin%20cash-addresses-".self::PHOLDER, "maxPage" => 21],
                    ["path" => "/top-100-dormant_".self::PHOLDER."y-bitcoin%20cash-addresses", "maxPage" => 9],
                ]
            ],
            [
                "cryptoType" => self::DASH,
                "cryptoRegex" => "/(X[1-9A-HJ-NP-Za-km-z]{33})/",
                "pages" => [
                    ["path" => "/top-100-richest-dash-addresses-".self::PHOLDER, "maxPage" => 100],
                    ["path" => "/top-100-busiest_by_sum-dash-addresses-".self::PHOLDER, "maxPage" => 21],
                    ["path" => "/top-100-busiest_by_transactions-dash-addresses-".self::PHOLDER, "maxPage" => 21],
                    ["path" => "/top-100-dormant_".self::PHOLDER."y-dash-addresses", "maxPage" => 9],
                ]
            ],
        ];
    }
}