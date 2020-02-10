<?php


namespace App\Console\Base\Common;

use App\Console\Constants\CryptoCurrency;
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
     * @param string ...$addresses
     * @return array
     */
    function getParsedAddresses(string $source, array $addresses, Crawler $crawler=null, string $cryptoRegex=null, string $cryptoType=null): array;
    
}