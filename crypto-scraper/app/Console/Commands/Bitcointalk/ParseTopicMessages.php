<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk;

use App\AddressMatcher;
use App\Console\BitcointalkParser;
use Symfony\Component\DomCrawler\Crawler;

class ParseTopicMessages extends BitcointalkParser
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitcointalk:parse_topics_messages {url} {verbose=1} {dateTime?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extracts crypto addresses from single topic page.';

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

        if (self::topicPageValid($this->url)) {
            $parsedAddresses = $this->getAddresses($this->url);
            //TODO save parsed addresses into tsv
            print_r($parsedAddresses);
//            $this->saveParsedData($this->dateTime, ...$parsedAddresses);
            return 1;
        } else {
            $this->printRedLine('Invalid main topic url: ' . $this->url);
            return 0;
        }
    }

    //TODO rewrite 
    private function getAddresses(string $url) {
        $crawler = $this->getPageCrawler($url);
        $title = $crawler->filter('title')->text();
        $results = $crawler->filter('.td_headerandpost')->each(function (Crawler $node) use($title) {
            $addresses = AddressMatcher::matchAddresses($node->html());
            $userInfo = $node->previousAll()->first();
            $userName = $userInfo->filter('a')->first()->text();
            $msgURL = $node->filter('a')->first()->attr('href');

            if($addresses) {
                return [
                    'username' => $userName,
                    'addresses' => $addresses,
                    'url' => $this->url,
                    'label' => $title,
                    'mdgUrl' => $msgURL,
                ];
            }
            return null;
        });
        
        return array_filter($results);
    }
    
    private static function topicPageValid(string $url): bool {
        return Utils::pageEntityValid('topic', $url);
    }
}
