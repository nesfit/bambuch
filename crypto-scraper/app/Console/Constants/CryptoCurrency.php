<?php
/**
 * Project: BitInfoCharts parser
 * Author: Vladislav Bambuch - xbambu03@stud.fit.vutbr.cz
 */

namespace App\Console\Constants;

abstract class CryptoCurrency {
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

}