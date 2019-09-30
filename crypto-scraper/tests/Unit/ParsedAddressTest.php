<?php

namespace Tests\Unit;

use App\Models\ParsedAddress;
use Tests\TestCase;

class ParsedAddressTest extends TestCase {
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testOwnerExists() {
        $testArray = [
            new ParsedAddress('testOwner1', '', '','','','',''),
            new ParsedAddress('testOwner2', '', '','','','',''),
            new ParsedAddress('testOwner3', '', '','','','','')
        ];
        
        $exists = ParsedAddress::ownerExists('testOwner2', ...$testArray);
        $this->assertEquals(true, $exists);

        $missing = ParsedAddress::ownerExists('testOwner', ...$testArray);
        $this->assertEquals(false, $missing);
        
        $empty = ParsedAddress::ownerExists('', ...$testArray);
        $this->assertEquals(false, $empty);
    }
}
