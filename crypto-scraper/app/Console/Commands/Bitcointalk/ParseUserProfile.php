<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk;

use App\Console\BitcointalkParser;
use App\Console\CryptoCurrency;
use App\Models\ParsedAddress;
use App\Models\Pg\Category;

class ParseUserProfile extends BitcointalkParser
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitcointalk:parse_user_profile {url} {verbose=1} {dateTime?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extracts bitcoin address from user profile.';

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
        
        if (self::profilePageValid($this->url)) {
            $source = $this->getFullHost();
            list($name, $address) = $this->parseProfile($this->url);

            if ($name) {
                $this->saveParsedData($this->dateTime, ...[
                    new ParsedAddress(
                        $name,
                        $this->url,
                        '',
                        $source,
                        $address,
                        CryptoCurrency::BTC["code"],
                        Category::CAT_2
                    )
                ]);
                return 1; 
            } else {
                $this->printRedLine('No name found in profile: ' . $this->url);
                return 0;
            }
        } else {
            $this->printRedLine('Invalid topic page url: ' . $this->url);
            return 0;
        }
    }


    private function parseProfile(string $url): array {
        $this->printVerbose2("<fg=white>Parsing profile: ". $url . "</>");

        $crawler = $this->getPageCrawler($url);
        $addressNode = $crawler->filterXPath('//text()[contains(.,"Bitcoin address: ")]/../../../td[last()]')->getNode(0);
        if ($addressNode) {
            $name = $crawler->filterXPath('//text()[contains(.,"Name")]/../../../td[last()]')->text();
            $address = $addressNode->nodeValue;
            return [$name,$address];
        }
        return [null,null];
    }

    private static function profilePageValid(string $url): bool {
        return preg_match('/https:\/\/bitcointalk.org\/index.php\?action=profile;u=\d+$/', $url, $matches) === 1;
    }
}
