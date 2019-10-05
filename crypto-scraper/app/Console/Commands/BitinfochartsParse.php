<?php

namespace App\Console\Commands;

use App\Console\Utils;

use App\Models\ParsedAddress;
use DOMXPath;

class BitinfochartsParse extends CryptoParser {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitinfocharts:parse {url} {verbose=2} {dateTime?} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse one bitinfocharts page';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $this->verbose = $this->argument("verbose");
        $dateTime = $this->argument("dateTime") || '';
        $url = $this->argument("url");

        $source = Utils::getFullHost($url);
        $cryptoSettings = Utils::getCryptoSettings($url);
        $cryptoRegex = $cryptoSettings["regex"];
        $cryptoType = $cryptoSettings["code"];
                
        $this->printParsingPage($url);
        
        $body = $this->getDOMBody($url);
        
        if (!$body) {
            $this->line("<fg=red>No body found.</>");
            exit();
        }

        $addresses = $this->getAddresses($body, $cryptoRegex);
        if (empty($addresses)) {
            $this->line("<fg=red>No addresses found.</>");
            exit();
        }

        $bodyXpath = Utils::getDOMXPath($body);

        $this->printHeader("<fg=yellow>Getting addresses from wallet:</>");
        $parsedAddresses = $this->getParsedAddresses($bodyXpath, $addresses, $cryptoRegex, $source, $cryptoType);
        // store wallets data into TSV file 
        $this->printHeader("<fg=yellow>Inserting owner:</>");
        $this->saveParsedData($dateTime, ...$parsedAddresses);
        return true;
    }

    /**
     * Core function. Extracts info about wallets and returns it in an array for each address owner.
     *
     * @param DOMXPath $bodyXpath Input for xpath
     * @param array $addresses All addresses extracted from single page
     * @param string $cryptoRegex Regex for additional address extraction
     * @param string $source schema://host extracted from an url
     * @return ParsedAddress[]
     */
    private function getParsedAddresses($bodyXpath, $addresses, $cryptoRegex, $source, $cryptoType): array {
        // TODO refactor to use array_map instead of foreach with side-effect
        $result = [];
        foreach ($addresses as $address) {
            $walletInfo = $this->getWalletInfo($bodyXpath, $address);
            // parse only useful wallets => no anonymous 
            if (!empty($walletInfo)) {
                $owner = $walletInfo["owner"];
                $link = $walletInfo["link"];
                $label = $walletInfo["label"];
                $url = $source . $link;
                $this->printDetail("- " . $owner . " ");
                // check if a wallet has been already parsed in this page
                
//                print_r($result);
                if (!ParsedAddress::ownerExists($owner, ...$result)) {
                    $body = Utils::getContentFromURL($url);
                    if ($body != "") {
                        // get addresses from a wallet
                        $walletAddresses = $this->getAddresses($body, $cryptoRegex);
                        // always insert also the original address
                        array_push($walletAddresses, $address);
                        // map addresses to ParseAddress structure
                        $result = array_reduce($walletAddresses, function ($acc, $address) use ($owner, $label, $url, $source, $cryptoType) {
                            $newItem = new ParsedAddress($owner, $url, $label, $source, $address, $cryptoType, '');
                            array_push($acc, $newItem);
                            return $acc;
                        }, $result);

                        $addressCount = sizeof($walletAddresses);
                        $this->printDetail("\t-> success: " . $addressCount . " addresses found. \n");
                    } else {
                        $newItem = new ParsedAddress($owner, $url, $label, $source, $address, $cryptoType, '');
                        array_push($result, $newItem);
                    }
                } else {
                    $newItem = new ParsedAddress($owner, $url, $label, $source, $address, $cryptoType, '');
                    array_push($result, $newItem);
                }
            }
        }
        return $result;
    }
    

    /**
     * Gets addresses from a body according to crypto regex.
     *
     * @param string $body Body of a page from where to get addresses
     * @param string $cryptoRegex Regex used to find correct addresses
     * @return array Array of found addresses
     */
    private function getAddresses($body, $cryptoRegex) {
        preg_match_all($cryptoRegex, $body, $matches, PREG_OFFSET_CAPTURE);
        $result = array_map(function($match) { return $match[0]; }, $matches[0]);
        return array_unique($result);
    }

    /**
     * Get owner, name and link of a wallet.
     *
     * @param DOMXPath $bodyXpath XPath of parsing page
     * @param string $address Address used for a wallet detection.
     * @return array Wallet info or empty array
     */
    private function getWalletInfo($bodyXpath, $address) {
        $nodeList = $bodyXpath->query("//text()[contains(.,'" .$address. "')]/../../small/a");
        // no label found for the address
        if ($nodeList->length) {
            $node = $nodeList->item(0);
            if ($node) {
                $label = $node->nodeValue;
                preg_match("/wallet: \d+/", $label, $anonymous, PREG_OFFSET_CAPTURE);
                // anonymous wallet => skip
                if (!empty($anonymous)) {
                    return [];
                }
                preg_match("/wallet: (.*)/", $label, $wallet);
                // unit owner names
                $ownerName = Utils::getOwnerName($label);
                return [
                    "owner" => $ownerName,
                    "link" => $node->getAttribute("href"),
                    "label" => $wallet[1]
                ];            
            }

        };
        return [];
    }
}
