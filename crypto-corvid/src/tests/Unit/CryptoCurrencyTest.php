<?php
declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Console\Constants\Common\CryptoCurrency;

class CryptoCurrencyTest extends TestCase {

    public function testGetName() {
        $this->assertEquals(CryptoCurrency::ETH['short'], CryptoCurrency::getShortcut(CryptoCurrency::ETH['code']));
        $this->assertEquals(CryptoCurrency::BTC['short'], CryptoCurrency::getShortcut(CryptoCurrency::BTC['code']));
        $this->assertEquals(CryptoCurrency::EMPTY['short'], CryptoCurrency::getShortcut(0));
        $this->assertEquals(CryptoCurrency::EMPTY['short'], CryptoCurrency::getShortcut(11));
    }
}
