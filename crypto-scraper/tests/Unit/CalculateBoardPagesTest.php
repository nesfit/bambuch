<?php

namespace Tests\Unit;

use App\Console\Commands\BitcointalkLoadBoards;
use Tests\TestCase;

class CalculateBoardPagesTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testCorrectValues() {
        $output = BitcointalkLoadBoards::calculateBoardPages(1, 0, 120);
        $expected = [
          'https://bitcointalk.org/index.php?board=1.0',  
          'https://bitcointalk.org/index.php?board=1.40',  
          'https://bitcointalk.org/index.php?board=1.80',  
          'https://bitcointalk.org/index.php?board=1.120'  
        ];
        $this->assertEquals($expected, $output);
    }
    
    public function testWrongBoundaries() {
        $output = BitcointalkLoadBoards::calculateBoardPages(1, 0, 110);
        $expected = [
          'https://bitcointalk.org/index.php?board=1.0',  
          'https://bitcointalk.org/index.php?board=1.40',  
          'https://bitcointalk.org/index.php?board=1.80'  
        ];
        $this->assertEquals($expected, $output);
    }   

    public function testTheSameBoundaries() {
        $output = BitcointalkLoadBoards::calculateBoardPages(1, 40, 40);
        $expected = [
            'https://bitcointalk.org/index.php?board=1.40'
        ];
        $this->assertEquals($expected, $output);
    }
}
