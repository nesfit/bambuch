<?php
declare(strict_types=1);

namespace Tests\Unit\Bitcointalk;

use App\Console\Commands\Bitcointalk\Kafka\BoardPagesProducer;
use Tests\TestCase;

class GetBoardIdTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testCorrectBoardPage() {
        $output = BoardPagesProducer::getBoardPageId('https://bitcointalk.org/index.php?board=83.11160');
        $this->assertEquals(11160, $output);
    }

    public function testWrongBoardPage() {
        $output = [
            BoardPagesProducer::getBoardPageId('https://bitcointalk.org/index.php?board=83.asdf'),
            BoardPagesProducer::getBoardPageId('https://bitcointalk.org/index.php?board=+š83.234'),
            BoardPagesProducer::getBoardPageId('https://bitcointalk.org/index.php?board=83.234asdf'),
            BoardPagesProducer::getBoardPageId('https://bitcointalk.org/index.php?board=83.'),
            BoardPagesProducer::getBoardPageId('https://bitcointalk.org/index.php?board=83.0')
        ];
        $expected = [ null, null, null, null, 0 ];
        $this->assertEquals($expected, $output);
    }

    public function testCorrectMainBoardId() {
        $output = BoardPagesProducer::getMainBoardId('https://bitcointalk.org/index.php?board=83.1');
        $this->assertEquals(83, $output);
    }

    public function testWrongMainBoardId() {
        $output = [
            BoardPagesProducer::getMainBoardId('https://bitcointalk.org/index.php?board=83.asdf'),
            BoardPagesProducer::getMainBoardId('https://bitcointalk.org/index.php?board=83.asdf234'),
            BoardPagesProducer::getMainBoardId('https://bitcointalk.org/index.php?board=83a.234'),
            BoardPagesProducer::getMainBoardId('https://bitcointalk.org/index.php?board=83.')
        ];
        $expected = [ null, null, null, null ];
        $this->assertEquals($expected, $output);
    }
}
