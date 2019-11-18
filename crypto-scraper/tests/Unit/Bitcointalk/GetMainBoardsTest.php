<?php
declare(strict_types=1);

namespace Tests\Unit\Bitcointalk;

use App\Console\Commands\Bitcointalk\DBLoaders\Boards as LoadBoards;
use Tests\TestCase;

class GetMainBoardsTest extends TestCase
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
        $output = LoadBoards::getMainBoards($input);
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
        $output = LoadBoards::getMainBoards($input);
        $this->assertEquals($expected, $output);
    }
    
    public function testEmptyBoards() {
        $input = [];
        $expected = [];
        $output = LoadBoards::getMainBoards($input);
        $this->assertEquals($expected, $output);
    }
    
    public function testBrokenBoards() {
        $input = [
            'https://bitcointalk.org/index.php?board=2.0',
            'https://bitcointalk.org/index.php?board=83.0;sort=replies'
        ];
        $expected = [
            'https://bitcointalk.org/index.php?board=2.0'
        ];
        $output = LoadBoards::getMainBoards($input);
        $this->assertEquals($expected, $output);
    }
}
