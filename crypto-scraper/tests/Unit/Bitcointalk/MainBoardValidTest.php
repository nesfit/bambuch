<?php
declare(strict_types=1);

namespace Tests\Unit\Bitcointalk;

use App\Console\Commands\Bitcointalk\Kafka\MainBoardsProducer;
use Tests\TestCase;

class MainBoardValidTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testValid() {
        $output = MainBoardsProducer::mainBoardValid('https://bitcointalk.org/index.php?board=83.0');
        $this->assertEquals(true, $output);
    }
    
    public function testBoardPage() {
        $output = MainBoardsProducer::mainBoardValid('https://bitcointalk.org/index.php?board=83.1');
        $this->assertEquals(false, $output);
    }
    
    public function testBrokenMainBoard() {
        $output = MainBoardsProducer::mainBoardValid('https://bitcointalk.org/index.php?board=83.0asdfsdf');
        $this->assertEquals(false, $output);
    }
}
