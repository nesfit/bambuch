<?php

namespace App\Console\Commands;

use App\Identity;
use DOMDocument;
use DOMXPath;
use Exception;
use Illuminate\Console\Command;

//supresses errors and warnings from the DOMDocument library for parsing HTML pages
error_reporting(E_ALL ^ E_WARNING);

class ParseBitcoin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitcoin:parse {addresses*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse bitcoin addresses and save them to DB with metadata';

    private $addresses = [];
    private $curl = false;

    private const METADATA_PAGE = [ "BTC" => "https://blockchain.info/address/", "LTC" => null, "ETH" => null ];

    //CUSTOMIZE THE ADDRESS EXTRACTION REGEXES HERE

    //source: https://rosettacode.org/wiki/Bitcoin/address_validation
    //bitcoin address starts with number 1 or 3, the rest is encoded in base58, which are characters 0-9, A-Z, a-z without there 4: 0, O, I, l
    private const BITCOIN = "/([13][a-km-zA-HJ-NP-Z1-9]{25,34})/";
    //stejne jako bitcoin, ale adresa zacina L, M nebo 3 === potencionalni kolize s bitcoin adresami zacinajicimi na 3
    private const LITECOIN = "/([LM3][a-km-zA-HJ-NP-Z1-9]{25,34})/";
    private const ETHEREUM = "/(0x[a-fA-F0-9]{40})/";

    private const regexes = [ "BTC" => self::BITCOIN, "LTC" => self::LITECOIN, "ETH" => self::ETHEREUM ];

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
        $this->addresses = $this->argument("addresses");

        $data = $this->parsePages();

        $i = 0;
        foreach ($data as $address => $metadata) {
            $identity = new Identity;
            $identity->address = $address;
            $identity->source = $metadata["title"];
            $identity->label = $metadata["metadata"];
            $identity->url = $metadata["original_url"];
            $identity->report_id = $i;
            $identity->save();

            $i++;
        }

        return;
    }
    /**
     * Extracts bitcoin addresses and metadata from URL addresses
     *
     * @return array Bitcoin addresses with metadata
     */
    private function parsePages() {
        $finalAddresses = [];
        $bar = $this->output->createProgressBar(count($this->addresses));

        foreach ($this->addresses as $url) {
            $addresses = $this->parsePage($url);
            //if the array is empty, there were no addresses on the URL, so skip to next address
            if ($addresses === false) continue;

            //add new addresses to already parsed
            $finalAddresses = array_merge($finalAddresses, $addresses);
            $bar->advance();
        }

        $bar->finish();

        return $finalAddresses;
    }

    /**
     * Parses one URL and extracts useful bitcoin addresses and metadata from it
     *
     * @param string $url URL to parse bitcoin addresses and metadata from
     * @param null $limit Customize how much addresses of a kind (bitcoin, litecoin, ...) you want to loop through (null = no limit)
     * @return array|bool Bitcoin addresses with metadata
     */
    private function parsePage($url, $limit = null) {
        $data = $this->getUrlContent($url, $this->curl);
        if ($data === false) return false;

        //extract title of the page from <title/> tag
        $title = $this->getTitle($data);

        //parse the addresses only from <body/>, not from <head/>
        $bodyStart = strpos($data, "<body");
        $bodyEnd = strrpos($data, "</body");

        $data = substr($data, $bodyStart, $bodyEnd - $bodyStart + strlen("</body>"));

        $doc = new DOMDocument();
        $doc->loadHTML($data);
            
        //initialiaze xpath instance on the html
        $xpath = new DOMXPath($doc);

        $potentialAdresses = $this->getPotentialAdresses($data);
        $addresses = [];

        //loop through all potential addresses
        foreach ($potentialAdresses as $abbrv => $addrs) {
            //loop through only one kind of addresses (btc, ltc, eth, ...)
            foreach($addrs as $i => $address) {
                //extract metadata from additional page
                $metadata = $this->getMetadata($abbrv, $address);

                //check if the address is valid
                if ($metadata !== -1) {
                    //check if the address is already in the $addresses array
                    if (!array_key_exists($address, $addresses)) {
                        //if it is not there, add a new address with metadata
                        $addresses[$address] = [ "type" => $abbrv, "metadata" => implode(",", $this->getPrimaryMetadata($xpath, $address)) ,"additional_metadata" => $metadata, "original_url" => $url, "title" => $title ];
                    } else {
                        //if it is there, add an addition kind of address, the metadata stay the same
                        $addresses[$address]["type"] .= ", " . $abbrv;
                    }
                }

                if ($limit !== null) {
                    //if there is a limitation set, break from the loop
                    if ($i >= $limit - 1) break;
                }
            }
        }

        return $addresses;
    }

    /**
     * Gets the URL content (HTML page)
     *
     * @param string $url URL to get the content from
     * @param bool $curl true = get the content via curl, false = get the content via file_get_contents
     * @return bool|mixed|string URL content
     */
    private function getUrlContent($url, $curl = false) {
        if ($curl) {
            //get the content via curl
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($curl, CURLOPT_TIMEOUT, 5);

            $data = curl_exec($curl);

            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            return ($httpcode >= 200 && $httpcode < 300) ? $data : false;
        } else {
            //get the content via built-in php file_get_contents function
            try {
                $data = file_get_contents($url);
                if ($data === false) {
                    return false;
                } else {
                    return $data;
                }
            } catch (Exception $e) {
                return false;
            }
        }
    }

    /**
     * Extract the title of the page
     *
     * @param string $data HTML content of the page
     * @return string Extracted title
     */
    private function getTitle($data) {
        $doc = new DOMDocument();
        $doc->loadHTML($data);

        $xpath = new DOMXPath($doc);
        $title = $xpath->query("//html/head/title");

        if ($title->length > 0) {
            return $title[0]->nodeValue;
        } else {
            //there is not <title/> tag in the <head/>
            return "No title";
        }
    }

    /**
     * Returns potential bitcoin (ltc, eth, ...) addresses from page
     *
     * @param string $data HTML page data to parse from
     * @return array Bitcoin addresses
     */
    private function getPotentialAdresses($data) {
        $result = [];

        //extract potential bitcoin addresses from page (!!!addresses could be invalid!!!)
        //loop through the different kinds of cryptocurrencies defined in regexes config array
        foreach (self::regexes as $abbrv => $regex) {
            //$abbrv = abbreviation = for example BTC, LTC, ...
            //$regex = address regex for the abbreviation
            $result[$abbrv] = [];
            preg_match_all($regex, $data, $matches, PREG_OFFSET_CAPTURE);

            //loop through all the matches (regex should match all the potential addresses on the page)
            foreach ($matches[0] as $match) {
                $addressToPush = $match[0];

                //push the address to the array only if there is NOT a string that contains this address
                //excludes potential LTC addresses which are only a substring of bitcoin addresses (and obviously invalid)
                //if there is already an address which is exactly the same as the new (to be pushed) address, add it anyway
                $canBePushed = true;
                foreach (array_keys($result) as $abbr) {
                    foreach ($result[$abbr] as $addr) {
                        if (strpos($addr, $addressToPush) !== false && $addr !== $addressToPush) {
                            $canBePushed = false;
                            break;
                        }
                    }
                }

                if ($canBePushed) array_push($result[$abbrv], $addressToPush);
            }

            $result[$abbrv] = array_unique($result[$abbrv], SORT_STRING);
        }

        return $result;
    }

    /**
     * Returns additional metadata if the supplied address is valid, otherwise returns -1
     * You can extend this method to validate the addresses
     * Works only for BTC
     *
     * @param string $abbrv Abbreviation of the address type
     * @param string $address Cryptocurrency address to get the additional metadata for
     * @return bool|int|string
     */
    private function getMetadata($abbrv, $address) {
        switch ($abbrv) {
            case "BTC":
                $data = $this->getUrlContent(self::METADATA_PAGE[$abbrv] . $address);
                if ($data === false) return -1;

                if (strlen($data) > 0) {
                    $doc = new DOMDocument();
                    $doc->loadHTML($data);

                    $xpath = new DOMXPath($doc);
                    $h1 = $xpath->query("//html/body/div[@class='container pt-100']/h1");
                    $small = $xpath->query("//html/body/div[@class='container pt-100']/h1/small");

                    if ($h1->length > 0 && $small->length > 0) {
                        return substr($h1[0]->nodeValue, 0, strlen($h1[0]->nodeValue) - strlen($small[0]->nodeValue) - 1);
                    }
                }

                return -1;

            case "LTC":
                return "No additional metadata";

            default:
                //probably invalid address
                return -1;
        }
    }

    /**
     * Extract metadata for cryptocurrency address from the page
     *
     * @param DOMXPath $xpath XPath object containing the page
     * @param string $address Cryptocurrency address to extract metadata for
     * @return array Extracted metadata
     */
    private function getPrimaryMetadata($xpath, $address) {
        $metadata = [];

        //get the parent element, whose child contains the cryptocurrency address as value
        $parent = $xpath->query("//*[contains(text(),'" . $address . "')]/..");
        foreach ($parent as $p) {
            //extract text from all the children of the parent element $text
            $split = explode("\n", $p->nodeValue);
            //iterate all the text values of the children
            foreach ($split as $i => $line) {
                //CUSTOMIZABLE PARAMETERS

                if ($line == "") continue; //do not extract empty strings
                if (strpos($line, $address) !== false) continue; //do not extract the address itself
                if (preg_match("/^\d+$/", $line)) continue; //do not extract lines which contain only numbers
                if ($i > 5) break; //if the children count is greater than 5, stop with extracting

                array_push($metadata, trim($line));
            }
        }

        return $metadata;
    }
}
