<?php

namespace App\Console\Commands;

use App\Console\CryptoCurrency;
use App\Console\Utils;
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
    protected $signature = 'bitcoinabuse:parse {url} {dateTime?} {verbose=2}';

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
            $reportPage = new ReportPage($browser, $url);
            $reports = $reportPage->process();
            
            $this->saveReports($reports, $address, $source, $dateTime);
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


    private function saveReports($reports, $address, $source, $dateTime) {
        foreach ($reports as $report) {
            $tsvData = Utils::createTSVData(
                $report['abuser'], $report['url'], $report['description'], $source, $address, CryptoCurrency::BTC["code"], $report['category']);
            $this->call("storage:write", [
                "data" => $tsvData, 
                "dateTime" => $dateTime,
                "verbose" => $this->verbose
            ]);
        }
    }
}

class ReportPage {
    private $browser;
    private $url;

    public function __construct(Goutte\Client $browser, string $url) {
        $this->browser = $browser;
        $this->url = $url;
    }

    public function process() {
        $page = $this->loadPage(1);
        $numPages = $page->filter('.page-item')->count() - 2; // -2 -> prev/next
        $reports = $this->parseReports($page);

        for ($i = 2; $i <= $numPages; $i++) {
            $page = $this->loadPage($i);
            $reports = array_merge($this->parseReports($page), $reports);
        }

        return $reports;
    }
    
    private function loadPage(int $pageNumber): Crawler {
        // delete history to prevent running out of memory
        $this->browser->restart();
        $pageUrl = $this->url . sprintf('?page=%s', $pageNumber);
        print $pageUrl . "\n";
        return $this->browser->request('GET', $pageUrl);
    }

    private function parseReports(Crawler $page) {
        return $page->filterXPath('//table[2]/tbody/tr')->each(function (Crawler $tr) {
            list($date, $category, $abuser, $description) = $tr->filter('td')->each(function ($td): Crawler {
                return $td;
            });

            return [
                'description' => trim($this->cleanText($description->text())),
                'category' => trim($category->text()),
                'abuser' => $this->getAbuserText($abuser),
                'url' => $this->url,
            ];
        });
    }

    private function cleanText($text) {
        $ascii = iconv("UTF-8", "UTF-8//TRANSLIT", $text);
        return str_replace(["\r", "\n", "\t"], ' ', $ascii);
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
