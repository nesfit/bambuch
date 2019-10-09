<?php


namespace App\Console;

use App\Models\ParsedAddress;
use Symfony\Component\DomCrawler\Crawler;

interface ParserInterface {
    /**
     * Gets addresses from a body according to crypto regex.
     * 
     * @param string $url
     * @param string $cryptoType
     * @return mixed
     */
    function getAddresses(string $url, string $cryptoType = CryptoCurrency::BTC["code"]);

    /**
     * @param string $source
     * @param Crawler|null $crawler
     * @param string|null $cryptoRegex
     * @param string|null $cryptoType
     * @param ParsedAddress ...$addresses
     * @return array
     */
    function getParsedAddresses(string $source, ?Crawler $crawler, ?string $cryptoRegex, ?string $cryptoType, ParsedAddress ...$addresses): array;
    
}