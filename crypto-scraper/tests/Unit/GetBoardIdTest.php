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
        $output = BitcointalkLoadBoards::getBoardId('https://bitcointalk.org/index.php?board=83.11160');
        $this->assertEquals(11160, $output);
    }

    public function testWringBoardPage() {
        $output = [
            BitcointalkLoadBoards::getBoardId('https://bitcointalk.org/index.php?board=83.asdf'),
            BitcointalkLoadBoards::getBoardId('https://bitcointalk.org/index.php?board=83.asdf234'),
            BitcointalkLoadBoards::getBoardId('https://bitcointalk.org/index.php?board=83.234asdf'),
            BitcointalkLoadBoards::getBoardId('https://bitcointalk.org/index.php?board=83.')
        ];
        $expected = [ null, null, null, null ];
        $this->assertEquals($expected, $output);
    }
}
