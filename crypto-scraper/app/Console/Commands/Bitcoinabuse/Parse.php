<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcoinabuse;

use App\Console\Constants\CryptoCurrency;
use App\Console\Base\Common\ParserInterface;
use App\Console\Base\Common\Utils;
use App\Models\Kafka\ParsedAddress;
use Symfony\Component\DomCrawler\Crawler;
use App\Console\Base\Common\CryptoParser;

class Parse extends CryptoParser implements ParserInterface
{
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
        parent::handle();

        $source = $this->getFullHost();
        
        list($hasNextPage, $addresses) = $this->getAddresses($this->url);
        
        $parsedAddresses = $this->getParsedAddresses($source, $addresses);
        $this->saveParsedData($this->dateTime, ...$parsedAddresses);
        // Artisan super-command cannot receive boolean values         
        return $hasNextPage ? 1 : 0;
    }

    public function getAddresses(string $url, string $cryptoType=''): array {
        $body = $this->getDOMBody($url);
        if ($body) {
            $json = json_decode($body->getContents());
            $addresses = array_map(function ($item) {return $item->address;}, $json->data);
            $hasNextPage = $json->next_page_url != null;

            return [$hasNextPage, $addresses];
        } else {
            return [false, []];
        }
    }

    public function getParsedAddresses(string $source, array $addresses, Crawler $crawler=null, string $cryptoRegex=null, string $cryptoType=null): array {
        $reports = [];
        foreach ($addresses as $address) {
            $url = $source . sprintf(self::REPORT_URL, $address);
            $this->printVerbose2("<fg=yellow>Getting report from page: ". $url . "</>");
            $page = $this->loadReportPage(1, $url);
            $numPages = $page->filter('.page-item')->count() - 2; // -2 -> prev/next
            array_push($reports, ...$this->parseReports($page, $url, $source, $address));
    
            for ($i = 2; $i <= $numPages; $i++) {
                $page = $this->loadReportPage($i, $url);
                array_push($reports, ...$this->parseReports($page, $url, $source, $address));
            }
        }
        
        return $reports;
    }

    private function loadReportPage(int $pageNumber, string $url): Crawler {
        $pageUrl = $url . sprintf('?page=%s', $pageNumber);
        $this->printVerbose3("<fg=white>Getting report from sub-page: ". $pageUrl . "</>");
        return $this->getPageCrawler($pageUrl);
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
