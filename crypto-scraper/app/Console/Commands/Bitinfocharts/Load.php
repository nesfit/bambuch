<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitinfocharts;

use DateTime;
use App\Console\Base\CryptoParser;

error_reporting(E_ALL ^ E_WARNING);

class Load extends CryptoParser {

    private const MAIN_URL = "https://bitinfocharts.com";
    private const PLACEHOLDER = "__placeholder__";
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitinfocharts:load {verbose=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse crypto addresses from bitinfocharts.com';

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
        $pages = $this->getPages();

        $start  = new DateTime();

        $this->printVerbosity();

        $this->parsePages($pages);
        
        $end = new DateTime();        
        $diff = $start->diff($end);
        $this->line("<fg=green>All work done in: ". $diff->format( '%H:%I:%S' ) .".</>");
    }

    /**
     * Core function. Parses configs, pages and inserts data into database.
     *
     * @param array $pages Configs with url and cryptocurrency data
     * @return void
     */
    private function parsePages($pages) {
        $dateTime = date("Y-m-d H:i:s");
        foreach ($pages as $page) {
            for ($i=1; $i < $page['maxPage']; $i++) {
                $url = $this->createPageUrl($page['path'], $i);
                $this->call("bitinfocharts:parse", [
                    "url" => $url,
                    "verbose" => $this->verbose,
                    "dateTime" => $dateTime
                ]);
            }
        }
    }


    /**
     * Concatenates main url with input path and index.
     * Puts the index to correct possition in the path using placeholder.
     *
     * @param string $path
     * @param int $index
     * @return string Full page url
     */
    private function createPageUrl(string $path, int $index) {
        return self::MAIN_URL . str_replace(self::PLACEHOLDER, $index, $path) . ".html";
    }

    /**
     * Gets configs with cryptocurrencies and assigned urls.
     *
     * @return array
     */
    private function getPages() {
        return [
            ["path" => "/top-100-richest-bitcoin-addresses-".self::PLACEHOLDER, "maxPage" => 100],
            ["path" => "/top-100-busiest_by_sum-bitcoin-addresses-".self::PLACEHOLDER, "maxPage" => 21],
            ["path" => "/top-100-busiest_by_transactions-bitcoin-addresses-".self::PLACEHOLDER, "maxPage" => 21],
            ["path" => "/top-100-dormant_".self::PLACEHOLDER."y-bitcoin-addresses", "maxPage" => 9],
            ["path" => "/top-100-richest-litecoin-addresses-".self::PLACEHOLDER, "maxPage" => 100],
            ["path" => "/top-100-busiest_by_sum-litecoin-addresses-".self::PLACEHOLDER, "maxPage" => 21],
            ["path" => "/top-100-busiest_by_transactions-litecoin-addresses-".self::PLACEHOLDER, "maxPage" => 21],
            ["path" => "/top-100-dormant_".self::PLACEHOLDER."y-litecoin-addresses", "maxPage" => 9],
            ["path" => "/top-100-richest-bitcoin%20gold-addresses-".self::PLACEHOLDER, "maxPage" => 100],
            ["path" => "/top-100-busiest_by_sum-bitcoin%20gold-addresses-".self::PLACEHOLDER, "maxPage" => 21],
            ["path" => "/top-100-busiest_by_transactions-bitcoin%20gold-addresses-".self::PLACEHOLDER, "maxPage" => 21],
            ["path" => "/top-100-dormant_".self::PLACEHOLDER."y-bitcoin%20gold-addresses", "maxPage" => 9],
            ["path" => "/top-100-richest-bitcoin%20cash-addresses-".self::PLACEHOLDER, "maxPage" => 100],
            ["path" => "/top-100-busiest_by_sum-bitcoin%20cash-addresses-".self::PLACEHOLDER, "maxPage" => 21],
            ["path" => "/top-100-busiest_by_transactions-bitcoin%20cash-addresses-".self::PLACEHOLDER, "maxPage" => 21],
            ["path" => "/top-100-dormant_".self::PLACEHOLDER."y-bitcoin%20cash-addresses", "maxPage" => 9],
            ["path" => "/top-100-richest-dash-addresses-".self::PLACEHOLDER, "maxPage" => 100],
            ["path" => "/top-100-busiest_by_sum-dash-addresses-".self::PLACEHOLDER, "maxPage" => 21],
            ["path" => "/top-100-busiest_by_transactions-dash-addresses-".self::PLACEHOLDER, "maxPage" => 21],
            ["path" => "/top-100-dormant_".self::PLACEHOLDER."y-dash-addresses", "maxPage" => 9]
        ];
    }
}
