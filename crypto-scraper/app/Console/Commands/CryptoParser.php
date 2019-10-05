<?php


namespace App\Console\Commands;


use App\Models\ParsedAddress;
use Illuminate\Console\Command;
use Goutte;
use GuzzleHttp;
use Psr\Http\Message\StreamInterface;

class CryptoParser extends Command 
{
    protected $verbose = 1;
    protected $description = 'Global command';
    protected $signature = 'global:command';
    protected $browser;
    
    public function __construct() {
        parent::__construct();
        $this->browser = new Goutte\Client();
    }

    /**
     * Print a string when verbose > 1.
     * Verbose output printing management.
     *
     * @param string $text Text to print
     * @return void
     */
    protected function printHeader($text) {
        if ($this->verbose > 1) {
            $this->line($text);
        }
    }

    /**
     * Print a string when verbose > 2.
     * Verbose output printing management.
     *
     * @param string $text Text to print
     * @return void
     */
    protected function printDetail($text) {
        if ($this->verbose > 2) {
            print($text);
        }
    }

    protected function saveParsedData(string $dateTime, ParsedAddress ...$parsedAddresses) {
        if (!empty($parsedAddresses)) {
            $progressBar = $this->output->createProgressBar(count($parsedAddresses));
            foreach ($parsedAddresses as $item) {
                $tsvData = $item->createTSVData();
                $this->call("storage:write", [
                    "data" => $tsvData,
                    "dateTime" => $dateTime,
                    "verbose" => $this->verbose
                ]);
                $progressBar->advance();
            }
            $progressBar->finish();
            $this->printHeader("");
        } else {
            $this->printDetail("- no data to insert.\n");
        }
    }
    
    protected function printParsingPage(string $url) {
        $this->line("<fg=cyan>Parsing page: " . $url ."</>");
    }
    
    protected function printVerbose() {
        $this->line("<fg=green>Starting with output verbosity: ". $this->verbose .".</>");
    }
    
    protected function getDOMBody(string $url): ?StreamInterface {
        try {
            return $this->browser
                        ->getClient()
                        ->request('GET', $url)
                        ->getBody();

        } catch (GuzzleHttp\Exception\GuzzleException $exception) {
            $this->error($exception);
            return null;
        }
    }
}