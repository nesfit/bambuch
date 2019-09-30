<?php


namespace App\Console;

use App\Models\ParsedAddress;

interface ParserInterface {
    public function saveParsedData(ParsedAddress $address);
}