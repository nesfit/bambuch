<?php

namespace Tests\Unit;

use App\Console\Commands\BitcointalkLoadBoards;
use Tests\TestCase;

class MainBoardValidTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testValid() {
        $output = BitcointalkLoadBoards::mainBoardValid('https://bitcointalk.org/index.php?board=83.0');
        $this->assertEquals(true, $output);
    }
    
    public function testBoardPage() {
        $output = BitcointalkLoadBoards::mainBoardValid('https://bitcointalk.org/index.php?board=83.1');
        $this->assertEquals(false, $output);
    }
    
    public function testBrokenMainBoard() {
        $output = BitcointalkLoadBoards::mainBoardValid('https://bitcointalk.org/index.php?board=83.0asdfsdf');
        $this->assertEquals(false, $output);
    }
}
