<?php

namespace App\Console\Commands;

use App\Console\Config;
use App\Console\Utils;
use App\Models\Pg\Address;
use App\Models\Pg\Category;
use App\Models\Pg\Identity;
use App\Models\Pg\Owner;

use App\Models\Pg\WalletExplorer;
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
        $url = $this->argument("url");

        $cryptoSettings = $this->getCryptoSettings($url);
        $cryptoRegex = $cryptoSettings["regex"];
        $cryptoType = $cryptoSettings["code"];
        $allWallets = []; // all wallets for one crypto currency
                
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

        if (!empty($allAddresses)) {
            $this->printHeader("<fg=yellow>Getting addresses from wallet:</>");
            $newWallets = $this->getNewWallets($allWallets, $bodyXpath, $allAddresses, $cryptoRegex);
            // insert new wallets into DB
            $this->printHeader("<fg=yellow>Inserting owner:</>");
            if (!empty($newWallets)) {
                $this->insertDataFromWallets($newWallets, $cryptoType);
                // merge new wallets and all wallets with the same crypto
                $allWallets = array_merge($allWallets, $newWallets);
            } else {
                $this->printDetail("- no data to insert.\n");
            }
        } else {
            $this->printHeader("<fg=magenta>No addresses found.</>");
        }
        $this->printHeader("");
    }

    private function getCryptoSettings(string $url) {
        switch (true) {
            case preg_match('/' . Config::BTC['name'] . '/' , $url): return Config::BTC;
            case preg_match('/' . Config::LTC['name']. '/', $url): return Config::LTC;
            case preg_match('/' . Config::BCH['name']. '/', $url): return Config::BCH;
            case preg_match('/' . Config::DASH['name']. '/', $url): return Config::DASH;
            case preg_match('/' . Config::BTG['name']. '/', $url): return Config::BTG;
        }
        return Config::EMPTY;
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
     * Core function. Parses configs, pages and inserts data into a database.
     *
     * @param array $allWallets All found interim wallets for a single cryptocurrency
     * @param DOMXPath $bodyXpath Input for xpath
     * @param array $allAddresses All addresses extracted from single page
     * @param string $cryptoRegex Regex for additional address extraction
     * @return array New wallets with assigned addresses
     */
    private function getNewWallets($allWallets, $bodyXpath, $allAddresses, $cryptoRegex) {
        $newWallets = [];
        foreach ($allAddresses as $address) {
            $walletInfo = $this->getWalletInfo($bodyXpath, $address);
            // parse only usefull wallets => no anonymous 
            if (!empty($walletInfo)) {
                $owner = $walletInfo["owner"];
                $link = $walletInfo["link"];
                $label = $walletInfo["label"];
                $url = Config::getUrl($link);
                $this->printDetail("- " . $owner . " ");
                // check if a wallet has been already parsed in this or previous page
                if (!array_key_exists($owner, $newWallets) && !array_key_exists($owner, $allWallets)) {
                    $body = Utils::getContentFromURL($url);
                    if ($body != "") {
                        // get addresses from a wallet
                        $walletAddresses = $this->getAddresses($body, $cryptoRegex);
                        // always insert also the original address
                        array_push($walletAddresses, $address);
                        $addressCount = sizeof($walletAddresses);
                        $this->printDetail("\t-> success: " . $addressCount . " addresses found. \n");
                        // if no address found, add at least the one from the previous page
                        $newWallets[$owner] = Utils::newWallet(
                            $url, $label, $addressCount ? $walletAddresses : [$address]
                        );
                    } else {
                        $newWallets[$owner] = Utils::newWallet($url, $label, [$address]);
                        $this->printDetail("\t-> failed to obtain HTML body. Adding one address at least. \n");
                    }
                } else if (array_key_exists($owner, $newWallets)) { // add one address into existing array
                    $prevWallet = $newWallets[$owner];
                    $prevUrl = $prevWallet["url"];
                    $prevLabel = $prevWallet["label"];
                    $prevAddresses = $prevWallet["addresses"];
                    array_push($prevAddresses, $address);
                    $newWallets[$owner] = Utils::newWallet($prevUrl, $prevLabel, $prevAddresses);
                    $this->printDetail("\t-> adding one address into existing array. \n");
                } else { // create entry in new wallets array
                    $newWallets[$owner] = Utils::newWallet($url, $label, [$address]);
                    $this->printDetail("\t-> adding one address into new array. \n");
                }
            }
        }
        return $newWallets;
    }



    /**
     * Insert owners, addresses and identities if there's no such a entry in the database.
     *
     * @param array $wallets Wallets to be inserted
     * @param int $cryptoType Type of cryptocurrency from config
     * @return void
     */
    private function insertDataFromWallets($wallets, $cryptoType) {
        foreach ($wallets as $ownerName => $data) {
            $this->printDetail("- " . $ownerName . "");
            $owner = Owner::getByName($ownerName);
            $url = $data["url"];
            $label = $data["label"];
            $source = Config::getSource();

            $countInserts = 0;
            foreach ($data["addresses"] as $address) {

                $category = $this->getCategory($ownerName);
                $existingAddress = Address::getByAddress($address);
                if ($existingAddress == null) { // no address in the database
                    $identity = $this->getNewIdentity($source, $url, $label);

                    $ownerAddr = new Address();
                    $ownerAddr->address = $address;
                    $ownerAddr->crypto = $cryptoType;
                    $ownerAddr->color = $category->color;
                    $ownerAddr->save();
                    $ownerAddr->identities()->save($identity);
                    $ownerAddr->categories()->attach($category->id);

                    $owner->addresses()->save($ownerAddr);
                    $countInserts++;
                } else if ($this->newIdentity($existingAddress->id, $source)) {
                    // no identity for the address in the database
                    $identity = $this->getNewIdentity($source, $url, $label);
                    $existingAddress->identities()->save($identity);
                }
            }
            $this->printDetail("\t-> inserted: " . $countInserts . " rows\n");
        }
    }


    /**
     * Get specific category from `Category` class based on owner name.
     *
     * @param string $ownerName
     * @return Category
     */
    private function getCategory(string $ownerName) {
        $owner = WalletExplorer::getByOwnerLike($ownerName);
        if ($owner) {
            return Category::getByName($owner->category);
        }
        return Category::getByName(Category::CAT_1);
    }

    /**
     * Checks if there is already an identity for specific combiantion of cryptoaddress and source url.
     * Enables adding new identities for existing cryptoaddress.
     *
     * @param string $addr_id Address id
     * @param string $newSource Source url of potential new identity
     * @return bool
     */
    private function newIdentity($addr_id, $newSource) {
        $identities = Identity::getIdentitiesByAddress($addr_id);
        $existingIdentities = $identities->reduce(function ($acc, $identity) {
            array_push($acc, $identity->source);
            return $acc;
        }, []);
        return in_array($newSource, $existingIdentities) == false;
    }

    private function getNewIdentity($source, $url, $label) {
        $identity = new Identity();
        $identity->source = $source;
        $identity->url = $url;
        $identity->label = $label;
        $identity->save();
        return $identity;
    }

    /**
     * Get owner, name and link of a wallet.
     *
     * @param DOMXPath $bodyXpath XPath of parsing page
     * @param string $address Address used for a wallet detection.
     * @return array Wallet info or empty array
     */
    private function getWalletInfo($bodyXpath, $address) {
        $node = $bodyXpath->query("//text()[contains(.,'" .$address. "')]/../../small/a");
        // no label found for the address
        if ($node->length) {
            $label = $node[0]->nodeValue;
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
                "link" => $node[0]->getAttribute("href"),
                "label" => $wallet[1]
            ];
        };
        return [];
    }
}
