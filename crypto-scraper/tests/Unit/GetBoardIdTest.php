<?php

namespace Tests\Unit;

use App\Console\Commands\BitcointalkLoadBoards;
use Tests\TestCase;

class GetBoardIdTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testCorrectBoardPage() {
        $output = BitcointalkLoadBoards::getBoardPageId('https://bitcointalk.org/index.php?board=83.11160');
        $this->assertEquals(11160, $output);
    }

    public function testWrongBoardPage() {
        $output = [
            BitcointalkLoadBoards::getBoardPageId('https://bitcointalk.org/index.php?board=83.asdf'),
            BitcointalkLoadBoards::getBoardPageId('https://bitcointalk.org/index.php?board=+š83.234'),
            BitcointalkLoadBoards::getBoardPageId('https://bitcointalk.org/index.php?board=83.234asdf'),
            BitcointalkLoadBoards::getBoardPageId('https://bitcointalk.org/index.php?board=83.')
        ];
        $expected = [ null, null, null, null ];
        $this->assertEquals($expected, $output);
    }
    
    public function testCorrectMainBoardId() {
        $output = BitcointalkLoadBoards::getMainBoardId('https://bitcointalk.org/index.php?board=83.1');
        $this->assertEquals(83, $output);
    }
    
    public function testWrongMainBoardId() {
        $output = [
            BitcointalkLoadBoards::getMainBoardId('https://bitcointalk.org/index.php?board=83.asdf'),
            BitcointalkLoadBoards::getMainBoardId('https://bitcointalk.org/index.php?board=83.asdf234'),
            BitcointalkLoadBoards::getMainBoardId('https://bitcointalk.org/index.php?board=83a.234'),
            BitcointalkLoadBoards::getMainBoardId('https://bitcointalk.org/index.php?board=83.')
        ];
        $expected = [ null, null, null, null ];
        $this->assertEquals($expected, $output);
    }
}
