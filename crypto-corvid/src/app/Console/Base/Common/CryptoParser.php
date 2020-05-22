<?php
declare(strict_types=1);

namespace App\Console\Base\Common;

use App\Models\Kafka\ParsedAddress;
use Illuminate\Console\Command;
use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;
use GuzzleHttp;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Log;
use Exception;

class CryptoParser extends Command {    
    protected $verbose = 1;
    protected $dateTime;
    protected $url;
    protected $print = true;
    protected string $serviceName = '';

    public function __construct() {
        parent::__construct();
        
        // TODO define custom progress bar with message
        ProgressBar::setFormatDefinition('custom', ' %percent% -- %message%');
    }
    
    public function handle() {
        if ($this->serviceName === '') {
            $this->errorGraylog("'serviceName' property is not set!");
            exit(0);
        }
        
        $this->verbose = $this->argument("verbose");
        $this->dateTime = $this->argument("dateTime") ?? date("Y-m-d H:i:s");
        $this->url = $this->hasArgument('url') ? $this->argument('url') : null;

        $this->print && $this->url && $this->printParsingPage($this->url);
    }


    private function makeRequest(string $url): array {
        // creating new browser every time causes regular proxy switching and better scraping performance 
        $browser = new Client(HttpClient::create(['proxy' => 'proxy:5566']));
        // to prevent traffic overloading + convert to milliseconds
        usleep(intval(env('SCRAPER_TIMEOUT', 2000)) * 1000);
        
        $this->infoGraylog("Requesting url", GraylogTypes::INFO, ["url" => $url]);

        $crawler = $browser->request('GET', $url);
        $response = $browser->getResponse();
        $content = $response->getContent();
        
        $this->infoGraylog("Response content size", GraylogTypes::INFO, [
            "url" => $url,
            "contentSize" => strlen($content)
        ]);
        
        $status = $response->getStatusCode();
        if ($status != 200) {
            $this->line("<fg=red>Page " . $url . " responded with status " . $status . "!</>");
            $this->warningGraylog("Failed to scrape page", ["status" => $status, "url" => $url]);
            sleep(1);
        }

        return [$crawler, $content];
    }

    protected function getPageCrawler(string $url): Crawler {
        [$crawler] = $this->makeRequest($url);
        return $crawler;
    }

    protected function getPageContent(string $url): string {
        [, $content] = $this->makeRequest($url);
        return $content->getContent();
    }

    protected function getFullHost(string $url = null): string {
        $parsedUrl = parse_url($url ?? $this->url);
        return $parsedUrl["scheme"] . "://" . $parsedUrl["host"];
    }

    private function graylogChannel(): LoggerInterface {
        return Log::channel('gelf');
    }

    private function getGraylogAttrs(array $context, string $logType, Exception $e = null): array {
        $general = ["serviceName" => $this->serviceName, "logType" => $logType];
        if ($e) {
            return array_merge($context, $general, ["errorMsg" => $e->getMessage()]);
        }
        return array_merge($context, $general);
    }

    public function infoGraylog(string $message, string $logType, array $context = []) {
        $attrs = $this->getGraylogAttrs($context, $logType);
        $this->graylogChannel()->info($message, $attrs);
        $this->info($message);
    }

    public function errorGraylog(string $message, Exception $e = null, array $context = []) {
        $attrs = $this->getGraylogAttrs($context, GraylogTypes::ERROR, $e);
        $this->graylogChannel()->error($message, $attrs);
        $this->error($message);
        if ($e) {
            report($e);
            $this->error($e->getMessage());
        }
    }

    public function warningGraylog(string $message, array $context = []) {
        $attrs = $this->getGraylogAttrs($context, GraylogTypes::WARN);
        $this->graylogChannel()->warning($message, $attrs);
        $this->warn($message);
    }

    public function debugGraylog(string $message, string $logType, array $context = []) {
        $attrs = $this->getGraylogAttrs($context, $logType);
        $this->graylogChannel()->debug($message, $attrs);
        $this->info($message);
    }
    
    /**
     * Print a string when verbose > 1.
     * Verbose output printing management.
     *
     * @deprecated 
     * @param string $text Text to print
     * @return void
     */
    protected function printVerbose2($text) {
        if ($this->verbose > 1) {
            $this->line($text);
        }
    }
    
    /**
     * Print a string when verbose > 2.
     * Verbose output printing management.
     *
     * @deprecated
     * @param string $text Text to print
     * @return void
     */
    protected function printVerbose3($text) {
        if ($this->verbose > 2) {
            $this->line($text);
        }
    }

    /**
     * Print a string when verbose > 2.
     * Verbose output printing management.
     *
     * @deprecated
     * @param string $text Text to print
     * @return void
     */
    protected function printDetail($text) {
        if ($this->verbose > 2) {
            print($text);
        }
    }

    /**
     * @deprecated
     */
    protected function printRedLine($text) {
        $this->line('<fg=red>' . $text . '</>');
    }

    /**
     * @deprecated
     */
    protected function printCyanLine($text) {
        $this->line('<fg=cyan>' . $text . '</>');
    }

    /**
     * @deprecated
     */
    protected function saveParsedData(string $dateTime, ParsedAddress ...$parsedAddresses) {
        if (!empty($parsedAddresses)) {
            $progressBar = $this->output->createProgressBar(count($parsedAddresses));
//            $progressBar->setFormat('custom');
//            $progressBar->setMessage('Saving results...');
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
            print("\n");
        } else {
            $this->printDetail("- no data to insert.\n");
        }
    }

    /**
     * @deprecated
     */
    protected function printParsingPage(string $url) {
        $this->line("<fg=cyan>Parsing page: " . $url ."</>");
    }

    /**
     * @deprecated
     */
    protected function printVerbosity() {
        $this->line("<fg=green>Starting with output verbosity: ". $this->verbose .".</>");
    }

    /**
     * @deprecated
     */
    protected function getDOMBody(string $url): ?StreamInterface {
        try {
            return $this->browser
                        ->getClient()
                        ->request('GET', $url)
                        ->getBody();

        } catch (GuzzleHttp\Exception\GuzzleException $exception) {
            $this->errorGraylog("Failed to get DOMBody", $exception, ["url" => $url]);
            return null;
        }
    }
}