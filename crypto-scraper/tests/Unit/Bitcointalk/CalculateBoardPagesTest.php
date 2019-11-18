<?php
declare(strict_types=1);

namespace Tests\Unit\Bitcointalk;

use App\Console\Commands\Bitcointalk\DBLoaders\Boards as LoadBoards;
use Tests\TestCase;

class CalculateBoardPagesTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testCorrectValues() {
        $output = LoadBoards::calculateBoardPages(1, 0, 80);
        $expected = [
          'https://bitcointalk.org/index.php?board=1.0',
          'https://bitcointalk.org/index.php?board=1.20',
          'https://bitcointalk.org/index.php?board=1.40',
          'https://bitcointalk.org/index.php?board=1.60',
          'https://bitcointalk.org/index.php?board=1.80'
        ];
        $this->assertEquals($expected, $output);
    }

    public function testWrongBoundaries() {
        $output = LoadBoards::calculateBoardPages(1, 0, 90);
        $expected = [
          'https://bitcointalk.org/index.php?board=1.0',
          'https://bitcointalk.org/index.php?board=1.20',
          'https://bitcointalk.org/index.php?board=1.40',
          'https://bitcointalk.org/index.php?board=1.60',
          'https://bitcointalk.org/index.php?board=1.80'
        ];
        $this->assertEquals($expected, $output);
    }

    public function testTheSameBoundaries() {
        $output = LoadBoards::calculateBoardPages(1, 40, 40);
        $expected = [
            'https://bitcointalk.org/index.php?board=1.40'
        ];
        $this->assertEquals($expected, $output);
    }
}
