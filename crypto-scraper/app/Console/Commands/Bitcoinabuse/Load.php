<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcoinabuse;

use App\Console\Base\Common\CryptoParser;

class Load extends CryptoParser {
    protected $signature = 'bitcoinabuse:load {verbose=1}';
    protected $description = 'Bitcoinabuse.com parser';
    protected $token = null;
    protected $browser = null;
    protected $dump = false;

    const URL = 'https://www.bitcoinabuse.com/api/reports/distinct?api_token=';
    const CATEGORY_MAP = [
        "ransomware" => 13,
        "darknet market" => 10,
        "bitcoin tumbler" => 5,
        "blackmail scam" => 11,
        "sextortion" => 11,
        "other" => 1,
    ];

    public function handle() {
        $this->verbose = $this->argument("verbose");
        $token = env('BITCOIN_ABUSE_TOKEN', '');
        $dateTime = date("Y-m-d H:i:s");

        $this->printVerbosity();
        
        $mainUrl = self::URL . $token;
        for ($page = 1;; $page++) {
            $url = $mainUrl  . "&page=" . $page;
            $hasNextPage = $this->call("bitcoinabuse:parse", [
                "url" => $url,
                "verbose" => $this->verbose,
                "dateTime" => $dateTime
            ]);
            
            if (!$hasNextPage) {
                break;
            }
        }
    }
}