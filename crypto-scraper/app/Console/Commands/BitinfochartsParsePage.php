<?php

namespace App\Console\Commands;

use App\Console\Utils;

use DOMXPath;

class BitinfochartsParsePage extends GlobalCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitinfocharts:parse {url} {verbose=2}';

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
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->verbose = $this->argument("verbose");
        $dateTime = $this->argument("dateTime");
        $url = $this->argument("url");

        $source = Utils::getFullHost($url);
        $cryptoSettings = Utils::getCryptoSettings($url);
        $cryptoRegex = $cryptoSettings["regex"];
        $cryptoType = $cryptoSettings["code"];
                
        $this->line("<fg=cyan>Parsing page: " . $url ."</>");
        
        $body = Utils::getContentFromURL($url);
        if ($body == "") {
            $this->line("<fg=red>No body found.</>");
            exit();
        }

        $allAddresses = $this->getAddresses($body, $cryptoRegex);
        if (empty($allAddresses)) {
            $this->line("<fg=red>No addresses found.</>");
            exit();
        }

        $bodyXpath = Utils::getDOMXPath($body);

        $this->printHeader("<fg=yellow>Getting addresses from wallet:</>");
        $wallets = $this->getWallets($bodyXpath, $allAddresses, $cryptoRegex, $source);
        // store wallets data into TSV file 
        $this->printHeader("<fg=yellow>Inserting owner:</>");
        if (!empty($wallets)) {
            foreach ($wallets as $owner => $data) {
                $this->printDetail("- " . $owner . "");
                foreach ($data['addresses'] as $address) {
                    $tsvData = Utils::createTSVData(
                        $owner, $url, $data['label'], $source, $address, $cryptoType, '');
                    $this->call("storage:write", [
                        "data" => $tsvData,
                        "dateTime" => $dateTime,
                        "verbose" => $this->verbose
                    ]);
                    $this->line("Stored into file");
                }
            }
        } else {
            $this->printDetail("- no data to insert.\n");
        }
        $this->printHeader("");
        return true;
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
     * Core function. Extracts info about wallets and returns it in an array for each address owner.
     *
     * @param DOMXPath $bodyXpath Input for xpath
     * @param array $allAddresses All addresses extracted from single page
     * @param string $cryptoRegex Regex for additional address extraction
     * @param string $source schema://host extracted from an url
     * @return array New wallets with assigned addresses
     */
    private function getWallets($bodyXpath, $allAddresses, $cryptoRegex, $source) {
        $resultWallets = [];
        foreach ($allAddresses as $address) {
            $walletInfo = $this->getWalletInfo($bodyXpath, $address);
            // parse only useful wallets => no anonymous 
            if (!empty($walletInfo)) {
                $owner = $walletInfo["owner"];
                $link = $walletInfo["link"];
                $label = $walletInfo["label"];
                $url = $source . $link;
                $this->printDetail("- " . $owner . " ");
                // check if a wallet has been already parsed in this page
                if (!array_key_exists($owner, $resultWallets)) {
                    $body = Utils::getContentFromURL($url);
                    if ($body != "") {
                        // get addresses from a wallet
                        $walletAddresses = $this->getAddresses($body, $cryptoRegex);
                        // always insert also the original address
                        array_push($walletAddresses, $address);
                        
                        $addressCount = sizeof($walletAddresses);
                        $this->printDetail("\t-> success: " . $addressCount . " addresses found. \n");
                        
                        // if no address found, add at least the one from the previous page
                        $resultWallets[$owner] = Utils::newWallet(
                            $url, $label, $addressCount ? $walletAddresses : [$address]
                        );
                    } else {
                        $resultWallets[$owner] = Utils::newWallet($url, $label, [$address]);
                        $this->printDetail("\t-> failed to obtain HTML body. Adding one address at least. \n");
                    }
                } else {
                    $prevWallet = $resultWallets[$owner];
                    $prevUrl = $prevWallet["url"];
                    $prevLabel = $prevWallet["label"];
                    $prevAddresses = $prevWallet["addresses"];
                    array_push($prevAddresses, $address);
                    $resultWallets[$owner] = Utils::newWallet($prevUrl, $prevLabel, $prevAddresses);
                    $this->printDetail("\t-> adding one address into existing array. \n");
                }
            }
        }
        return $resultWallets;
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
