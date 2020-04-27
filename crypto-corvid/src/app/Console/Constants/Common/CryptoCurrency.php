<?php
declare(strict_types=1);

namespace App\Console\Constants\Common;

abstract class CryptoCurrency {
    const BTC   = ['short' => 'BTC', 'name' => 'bitcoin', 'code' => 1, 'regex' => '/([13][a-km-zA-HJ-NP-Z1-9]{25,34})/'];
    const LTC   = ['short' => 'LTC', 'name' => 'litecoin', 'code' => 2, 'regex' => '/([LM3][a-km-zA-HJ-NP-Z1-9]{25,34})/'];
    const DASH  = ['short' => 'DASH', 'name' => 'dash', 'code' => 3, 'regex' => '/(X[1-9A-HJ-NP-Za-km-z]{33})/'];
    const ZEC   = ['short' => 'ZEC', 'name' => 'zcash', 'code' => 4, 'regex' => ''];
    const BCH   = ['short' => 'BCH', 'name' => 'bitcoin cash', 'code' => 5, 'regex' => '/([13][a-km-zA-HJ-NP-Z1-9]{25,34})/'];
    const DGB   = ['short' => 'DGB', 'name' => 'digibyte', 'code' => 6, 'regex' => ''];
    const BTG   = ['short' => 'BTG', 'name' => 'bitcoin gold', 'code' => 7, 'regex' => '/([AG][a-km-zA-HJ-NP-Z1-9]{25,34})/'];
    const QTUM  = ['short' => 'QTUM', 'name' => 'qtum', 'code' => 8, 'regex' => ''];
    const ETH   = ['short' => 'ETH', 'name' => 'ethereum', 'code' => 10, 'regex' => ''];
    const EMPTY = ['short' => 'EMPTY', 'name' => 'empty', 'code' => -1, 'regex' => 'empty'];

    const CRYPTO = [self::BTC, self::LTC, self::DASH, self::ZEC, self::BCH, self::DGB, self::BTG, self::QTUM, self::ETH];
    
    public static function getShortcut(int $code): string {
        return array_reduce(self::CRYPTO, function (string $acc, array $val) use ($code) {
            if ($val['code'] === $code) {
                return $val['short'];
            }
            return $acc;
        }, self::EMPTY['short']);
    }
}