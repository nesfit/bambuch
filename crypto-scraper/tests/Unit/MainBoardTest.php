<?php

namespace Tests\Unit;

use App\Console\Commands\BitcointalkLoadBoards;
use Tests\TestCase;

class MainBoardTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testAllMainBoards() {
        $input = [
            'https://bitcointalk.org/index.php?board=2.0',
            'https://bitcointalk.org/index.php?board=22.0',
            'https://bitcointalk.org/index.php?board=22222.0'
        ];
        $output = BitcointalkLoadBoards::getMainBoards($input);
        $this->assertEquals($input, $output);
    }
    
    public function testSomeMainBoards() {
        $input = [
            'https://bitcointalk.org/index.php?board=2.0',
            'https://bitcointalk.org/index.php?board=22.0',
            'https://bitcointalk.org/index.php?board=22222.0',
            'https://bitcointalk.org/index.php?board=22.1234'
        ];
        $expected = [
            'https://bitcointalk.org/index.php?board=2.0',
            'https://bitcointalk.org/index.php?board=22.0',
            'https://bitcointalk.org/index.php?board=22222.0'
        ];
        $output = BitcointalkLoadBoards::getMainBoards($input);
        $this->assertEquals($expected, $output);
    }
    
    public function testEmptyBoards() {
        $input = [];
        $expected = [];
        $output = BitcointalkLoadBoards::getMainBoards($input);
        $this->assertEquals($expected, $output);
    }
}
