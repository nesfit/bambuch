<?php
declare(strict_types=1);

namespace Tests\Unit\Bitcointalk;

use App\Console\Commands\Bitcointalk\DBLoaders\Boards as LoadBoards;
use Tests\TestCase;

class GetBoardIdTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testCorrectBoardPage() {
        $output = LoadBoards::getBoardPageId('https://bitcointalk.org/index.php?board=83.11160');
        $this->assertEquals(11160, $output);
    }

    public function testWrongBoardPage() {
        $output = [
            LoadBoards::getBoardPageId('https://bitcointalk.org/index.php?board=83.asdf'),
            LoadBoards::getBoardPageId('https://bitcointalk.org/index.php?board=+š83.234'),
            LoadBoards::getBoardPageId('https://bitcointalk.org/index.php?board=83.234asdf'),
            LoadBoards::getBoardPageId('https://bitcointalk.org/index.php?board=83.'),
            LoadBoards::getBoardPageId('https://bitcointalk.org/index.php?board=83.0'),
            LoadBoards::getBoardPageId('https://bitcointalk.org/index.php?board=83.1')
        ];
        $expected = [ null, null, null, null, 0, 1 ];
        $this->assertEquals($expected, $output);
    }

    public function testCorrectMainBoardId() {
        $output = LoadBoards::getMainBoardId('https://bitcointalk.org/index.php?board=83.1');
        $this->assertEquals(83, $output);
    }

    public function testWrongMainBoardId() {
        $output = [
            LoadBoards::getMainBoardId('https://bitcointalk.org/index.php?board=83.asdf'),
            LoadBoards::getMainBoardId('https://bitcointalk.org/index.php?board=83.asdf234'),
            LoadBoards::getMainBoardId('https://bitcointalk.org/index.php?board=83a.234'),
            LoadBoards::getMainBoardId('https://bitcointalk.org/index.php?board=83.')
        ];
        $expected = [ null, null, null, null ];
        $this->assertEquals($expected, $output);
    }
}
