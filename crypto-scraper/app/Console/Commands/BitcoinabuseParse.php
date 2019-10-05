<?php

namespace App\Console\Commands;

use App\Console\CryptoCurrency;
use App\Console\Utils;
use App\Models\ParsedAddress;
use Goutte;
use GuzzleHttp;
use Symfony\Component\DomCrawler\Crawler;

class BitcoinabuseParse extends GlobalCommand {
    const REPORT_URL = '/reports/%s';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitcoinabuse:parse {url} {verbose=2} {dateTime?} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $dateTime = $this->argument("dateTime");
        $url = $this->argument('url');

        $source = Utils::getFullHost($url);
        $browser = new Goutte\Client();

        list($hasNextPage, $addresses) = $this->loadDistinct($url, $browser);
        
        foreach ($addresses as $address) {
            $url = $source . sprintf(self::REPORT_URL, $address);
            $this->printHeader("<fg=yellow>Getting report from page: ". $url . "</>");
            $reports = $this->process($browser, $url, $source, $address);
            $this->saveParsedData($dateTime, ...$reports);
            break; //TODO REMOVE
        }
        
        return $hasNextPage;
    }

    private function loadDistinct(string $url, Goutte\Client $browser): array {
        try {
            $response = $browser->getClient()->request('GET', $url);
            $body = $response->getBody();
    
            $json = json_decode($body->getContents());
            $addresses = array_map(function ($item) {return $item->address;}, $json->data);
            $hasNextPage = $json->next_page_url != null;
            
            return [$hasNextPage, $addresses];
            
        } catch (GuzzleHttp\Exception\GuzzleException $exception) {
            $this->error($exception);
        }
        return [false, []];
    }
    

    public function process(Goutte\Client $browser, string $url, $source, $address) {
        $page = $this->loadPage(1, $browser, $url);
        $numPages = $page->filter('.page-item')->count() - 2; // -2 -> prev/next
        $reports = $this->parseReports($page, $url, $source, $address);

        for ($i = 2; $i <= $numPages; $i++) {
            $page = $this->loadPage($i, $browser, $url);
            $reports = array_merge($this->parseReports($page, $url, $source, $address), $reports);
        }

        return $reports;
    }

    private function loadPage(int $pageNumber, Goutte\Client $browser, string $url): Crawler {
        // delete history to prevent running out of memory
        $browser->restart();
        $pageUrl = $url . sprintf('?page=%s', $pageNumber);
        print $pageUrl . "\n";
        return $browser->request('GET', $pageUrl);
    }

    /**
     * @param Crawler $page
     * @param string $url
     * @param $source
     * @param $address
     * @return ParsedAddress[]
     */
    private function parseReports(Crawler $page, string $url, $source, $address): array {
        return $page->filterXPath('//table[2]/tbody/tr')->each(function (Crawler $tr) use ($url, $source, $address){
            // get all "td" elements from the page
            list($date, $category, $abuser, $description) = $tr->filter('td')->each(function ($td): Crawler {
                return $td;
            });

            return new ParsedAddress(
                $this->getAbuserText($abuser), 
                $url, 
                trim($description->text()), 
                $source, 
                $address, 
                CryptoCurrency::BTC["code"], 
                trim($category->text())
            );
        });
    }

    private function getAbuserText(Crawler $abuserNode): string {
        $emailNode = $abuserNode->filter('.__cf_email__');
        if ($emailNode->count()) {
            $encryptedData = $emailNode->first()->attr('data-cfemail');
            return Utils::decrypt($encryptedData);
        } else {
            return trim(str_replace(';', '', $abuserNode->text()));
        }
    }
}
