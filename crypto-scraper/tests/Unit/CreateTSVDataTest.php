<?php

namespace Tests\Unit;

use App\Console\Utils;
use Tests\TestCase;

class CreateTSVDataTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testEmptyData()
    {
        $str = Utils::createTSVData('', '', '', '', '', '', '');
        $actual = unpack("C*", $str);
        $expected = [1 => 9, 2 => 9, 3 => 9, 4 => 9, 5 => 9, 6 => 9];
        $this->assertEquals($expected, $actual);
    }
    
    public function testConvertUTF()
    {
        $str = Utils::createTSVData('é', '', '', '', '', '', '');
        $actual = unpack("C*", $str);
        $expected = [1 => 195, 2 => 169, 3 => 9, 4 => 9, 5 => 9, 6 => 9, 7 => 9, 8 => 9];
        $this->assertEquals($expected, $actual);
    }
    
    public function testTabInString()
    {
        $str = Utils::createTSVData("\t", '', '', '', '', '', '');
        $actual = unpack("C*", $str);
        $expected = [1 => 32, 2 => 9, 3 => 9, 4 => 9, 5 => 9, 6 => 9, 7 => 9];
        $this->assertEquals($expected, $actual);
    }
}
