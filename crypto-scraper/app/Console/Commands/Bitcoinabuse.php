<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

use App\AddressMatcher;
use App\Models\Pg_xvokra00\Category;
use App\Models\Pg_xvokra00\Address;
use App\Models\Pg_xvokra00\Identity;
use App\Models\Pg_xvokra00\Owner;


//token: GJ82AaCsEzpHayaOc3HGaj8xTWvafSkU0Wnkjll7hcw3oDtx2izStyqr7TN8

class Bitcoinabuse extends Command
{
    protected $signature = 'bitcoinabuse:parse {--dump} {token}';
    protected $description = 'Bitcoinabuse.com parser';
    protected $token = null;
    protected $browser = null;
    protected $dump = false;

    const URL = 'https://www.bitcoinabuse.com';
    const DISTINCT_URL = '/api/reports/distinct?api_token=%s&page=%s';
    const CATEGORY_MAP = [
        "ransomware" => 13,
        "darknet market" => 10,
        "bitcoin tumbler" => 5,
        "blackmail scam" => 11,
        "sextortion" => 11,
        "other" => 1,
    ];

    public function handle()
    {
        $this->token = $this->argument('token');
        $this->dump = $this->option('dump');

        $this->browser = new \Goutte\Client();

        for ($page = 1;; $page++) {
            list($hasNextPage, $addresses) = $this->loadDistinct($page);

            foreach ($addresses as $address) {
                $reportPage = new ReportPage($this->browser, $address);
                $reports = $reportPage->process();
                $this->saveReports($reports, $address);
            }

            if (!$hasNextPage) {
                break;
            }
            sleep(2);
        }
    }

    public function loadDistinct($page)
    {
        $url = self::URL . sprintf(self::DISTINCT_URL, $this->token, $page);
        $response = $this->browser->getClient()->request('GET', $url);

        if ($response->getStatusCode() != 200) {
            throw new \Exception($response->getStatusCode());
        }

        $body = $response->getBody();

        $json = json_decode($body->getContents());
        $addresses = array_map(function ($item) {return $item->address;}, $json->data);
        $hasNextPage = $json->next_page_url != null;
        return [$hasNextPage, $addresses];
    }

    public function saveReports($reports, $address)
    {
        foreach ($reports as $report) {
            $report['address'] = $address;

            if ($this->dump) {
                $this->printReport($report);
            } else {
                print "saving something";
                $this->saveReport($report);
            }
        }
    }

    public function saveReport($report)
    {
        $coins = AddressMatcher::identifyAddress($report['address']);
        foreach ($coins as $coin) {
            $address_db = Address::firstOrCreate([
                Address::COL_ADDRESS => $report['address'],
                Address::COL_CRYPTO => $coin,
                Address::COL_OWNER => null,
            ]);
            $category = Category::find($this->getCategoryID($report['category']));
            $address_db->categories()->sync($category, $detach = false);
            $identity_db = Identity::firstOrCreate([
                Identity::COL_SOURCE => 'bitcoinabuse.com',
                Identity::COL_LABEL => $report['abuser'] ?? substr($report['description'], 0, 255),
                Identity::COL_URL => $report['url'],
                Identity::COL_DESC => $report['description'],
                Identity::COL_ADDRID => $address_db->getKey(),
            ]);
        }
    }

    public function printReport($report)
    {
        $serialized = sprintf(
            "%s;%s;%s;%s;%s\n",
            $report['address'],
            $report['abuser'],
            $report['category'],
            $report['url'],
            $report['description']
        );
        echo $serialized;
    }

    public function getCategoryID($category)
    {
        return self::CATEGORY_MAP[$category];
    }
}


class ReportPage
{
    const REPORT_URL = '/reports/%s';

    public function __construct(\Goutte\Client $browser, string $address)
    {
        $this->browser = $browser;
        $this->url = Bitcoinabuse::URL . sprintf(self::REPORT_URL, $address);
        $this->address = $address;
    }

    public function process()
    {
        $page = $this->loadPage(1);
        $numPages = $page->filter('.page-item')->count() - 2; // -2 -> prev/next
        $reports = $this->parseReports($page);

        for ($i = 2; $i <= $numPages; $i++) {
            $page = $this->loadPage($i);
            $reports = array_merge($this->parseReports($page), $reports);
        }

        return $reports;
    }

    public function loadPage($pageNumber)
    {
        // delete history to prevent running out of memory
        $this->browser->restart();
        $pageUrl = $this->url . sprintf('?page=%s', $pageNumber);
        return $this->browser->request('GET', $pageUrl);
    }

    public function parseReports($page)
    {
        return $page->filterXPath('//table[2]/tbody/tr')->each(function ($tr) {
            list($date, $category, $abuser, $description) = $tr->filter('td')->each(function ($td) {
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

    public function cleanText($text)
    {
        $ascii = iconv("UTF-8", "utf-8//TRANSLIT", $text);
        return str_replace(["\r", "\n"], ' ', $ascii);
    }


    public function getAbuserText($abuserNode)
    {
        $emailNode = $abuserNode->filter('.__cf_email__');

        if ($emailNode->count()) {
            $encryptedData = $emailNode->first()->attr('data-cfemail');
            $email = EmailDecrypter::decrypt($encryptedData);
        } else {
            return trim(str_replace(';', '', $abuserNode->text()));
        }
    }
}

class EmailDecrypter
{
    public static function extractXorCode($address, $position)
    {
        $value = substr($address, $position, 2);
        return hexdec($value);
    }

    public static function decrypt($address)
    {
        $output = "";
        $xor_base = self::extractXorCode($address, 0);

        for ($i = 2; $i < strlen($address); $i += 2) {
            $char_code = self::extractXorCode($address, $i) ^ $xor_base;
            $output .= chr($char_code);
        }

        return $output;
    }
}