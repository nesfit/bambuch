<?php

namespace Tests\Unit\Bitcointalk;

use App\Console\Commands\Bitcointalk\LoadBoards;
use Tests\TestCase;

class GetBoardPagesTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testBoardPages() {
        $input = [
            'https://bitcointalk.org/index.php?board=2.234',
            'https://bitcointalk.org/index.php?board=22.33',
            'https://bitcointalk.org/index.php?board=22222.23453453234'
        ];
        $output = LoadBoards::getBoardPages($input);
        $this->assertEquals($input, $output);
    }
    
    public function testSomeBoardPages() {
        $input = [
            'https://bitcointalk.org/index.php?board=22.234543',
            'https://bitcointalk.org/index.php?board=22.1234',
            'https://bitcointalk.org/index.php?board=2.0',
            'https://bitcointalk.org/index.php?board=22222.0'
        ];
        $expected = [
            'https://bitcointalk.org/index.php?board=22.234543',
            'https://bitcointalk.org/index.php?board=22.1234'
        ];
        $output = LoadBoards::getBoardPages($input);
        $this->assertEquals($expected, $output);
    }
    
    public function testEmptyBoardPages() {
        $input = [];
        $expected = [];
        $output = LoadBoards::getBoardPages($input);
        $this->assertEquals($expected, $output);
    }
    
    public function testBrokenBoardPages() {
        $input = [
            'https://bitcointalk.org/index.php?board=2.2345',
            'https://bitcointalk.org/index.php?board=83.2345;sort=replies'
        ];
        $expected = [
            'https://bitcointalk.org/index.php?board=2.2345'
        ];
        $output = LoadBoards::getBoardPages($input);
        $this->assertEquals($expected, $output);
    }
}