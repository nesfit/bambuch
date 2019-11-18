<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitinfocharts;

use App\Console\CryptoCurrency;
use App\Console\ParserInterface;
use App\Console\Utils;

use App\Models\ParsedAddress;
use Symfony\Component\DomCrawler\Crawler;
use App\Console\CryptoParser;


class Parse extends CryptoParser implements ParserInterface {
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
        parent::handle();

        $source = $this->getFullHost();
        $cryptoSettings = Utils::getCryptoSettings($this->url);
        $cryptoRegex = $cryptoSettings["regex"];
        $cryptoType = $cryptoSettings["code"];
                
        
        $body = $this->getDOMBody($this->url);
        
        if (!$body) {
            $this->line("<fg=red>No body found.</>");
            exit();
        }

        $addresses = $this->getAddresses($this->url, $cryptoRegex);

        if (empty($addresses)) {
            $this->line("<fg=red>No addresses found.</>");
            exit();
        }

        $pageCrawler = $this->getPageCrawler($this->url);

        $this->printVerbose2("<fg=yellow>Getting addresses from wallet:</>");
        $parsedAddresses = $this->getParsedAddresses($source, $addresses, $pageCrawler, $cryptoRegex, $cryptoType);
        // store wallets data into TSV file 
        $this->printVerbose2("<fg=yellow>Inserting owner:</>");
        $this->saveParsedData($this->dateTime, ...$parsedAddresses);
        return true;
    }


    public function getParsedAddresses(string $source, array $addresses, Crawler $crawler=null, string $cryptoRegex=null, string $cryptoType=null): array {
        // TODO refactor to use array_map instead of foreach with side-effect
        $result = [];
        foreach ($addresses as $address) {
            $walletInfo = $this->getWalletInfo($crawler, $address);
            // parse only useful wallets => no anonymous 
            if (!empty($walletInfo)) {
                $owner = $walletInfo["owner"];
                $link = $walletInfo["link"];
                $label = $walletInfo["label"];
                $url = $source . $link;
                $this->printDetail("- " . $owner . " ");
                // check if a wallet has been already parsed in this page
                
                if (!ParsedAddress::ownerExists($owner, ...$result)) {
                    $walletAddresses = $this->getAddresses($url, $cryptoRegex);
                    if (!empty($walletAddresses)) {
                        // get addresses from a wallet
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
     * @param string $url
     * @param string $cryptoRegex
     * @return array
     */
    public function getAddresses(string $url, string $cryptoRegex = CryptoCurrency::BTC["code"]): array {
        $maybeBody = $this->getDOMBody($url);
        if ($maybeBody) {
            $body = $maybeBody->getContents();
            preg_match_all($cryptoRegex, $body, $matches, PREG_OFFSET_CAPTURE);
            $result = array_map(function($match) { return $match[0]; }, $matches[0]);
            return array_unique($result);
        }
        return [];
    }

    private function getWalletInfo(Crawler $crawler, $address) {
        $node = $crawler->filterXPath("//text()[contains(.,'" .$address. "')]/../../small/a")->getNode(0);
        // no label found for the address
        if ($node) {
            $label = $node->nodeValue;
            preg_match("/wallet: \d+/", $label, $anonymous, PREG_OFFSET_CAPTURE);
            // anonymous wallet => skip
            if (!empty($anonymous)) {
                return [];
            }
            preg_match("/wallet: (.*)/", $label, $wallet);
            // unify owner names
            $ownerName = Utils::getOwnerName($label);
            return [
                "owner" => $ownerName,
                "link" => $node->getAttribute("href"),
                "label" => $wallet[1]
            ];            
        }

        return [];
    }
}
