<?php
declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Kafka\ParsedAddress;
use Tests\TestCase;

class ParsedAddressTest extends TestCase {
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testOwnerExists() {
        $testArray = [
            new ParsedAddress('testOwner1', '', '','','',1,''),
            new ParsedAddress('testOwner2', '', '','','',1,''),
            new ParsedAddress('testOwner3', '', '','','',1,'')
        ];
        
        $exists = ParsedAddress::ownerExists('testOwner2', ...$testArray);
        $this->assertEquals(true, $exists);

        $missing = ParsedAddress::ownerExists('testOwner', ...$testArray);
        $this->assertEquals(false, $missing);
        
        $empty = ParsedAddress::ownerExists('', ...$testArray);
        $this->assertEquals(false, $empty);
    }
}
