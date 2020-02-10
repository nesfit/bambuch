<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;
use Clue\React\Buzz\Browser;
use Clue\React\Block;
use Clue\React\HttpProxy\ProxyConnector as HttpConnectClient;
use React\EventLoop;
use React\Socket\Connector;
use Psr\Http\Message\ResponseInterface;
use SplStack;

use App\AddressMatcher;
use App\Models\Pg\Category;
use App\Models\Pg\Address;
use App\Models\Pg\Identity;
use App\Models\Pg\Owner;
//pcntl_async_signals(true);


class Bitcointalk extends Command
{
    protected $signature = 'bitcointalk:parse {proxyFile?}';
    protected $description = 'Bitcointalk.com parser';
    private $proxyList;
    private $loop;
    private $stack;
    private $run = false;
    private $stackDumpTimer = NULL;
    private $activeBrowsers = 0;

    const URL = 'https://bitcointalk.org/index.php';
    const STACK_FILE = 'stack';

    public function __construct()
    {
        parent::__construct();
        $this->loop = EventLoop\Factory::create();
        $this->stack = new SplStack();
    }

    public function handle()
    {
        print("initBrowsers \n");
        $this->initBrowsers();
        print("initStack \n");
        $this->initStack();

        $sighandler = [$this, 'sighandler'];

//        pcntl_signal(SIGINT, $sighandler);
//        pcntl_signal(SIGHUP,  $sighandler);
//        pcntl_signal(SIGTERM,  $sighandler);

        foreach ($this->browsers as $browser) {
            $this->registerTick($browser);
            $this->activeBrowsers++;
        }

        $this->stackDumpTimer = $this->loop->addPeriodicTimer(60*30, function() {
            if($this->stack->isEmpty() && $this->activeBrowsers == 0) {
                $this->loop->cancelTimer($this->stackDumpTimer);
            } else {
                $this->saveStackToFile(self::STACK_FILE);
            }
        });

        $this->loop->run();
        $this->saveStackToFile(self::STACK_FILE);
    }
    
    public function initBrowsers()
    {
        $proxyFile = $this->argument('proxyFile');
        if ($proxyFile) {
            foreach ($this->loadProxyList($proxyFile) as $proxyUrl) {
                $this->browsers[] = new ProxyBrowser($this->loop, $proxyUrl);
            }
        } else {
            $this->browsers[] = new DefaultBrowser($this->loop);
        }
    }
    
    public function loadProxyList($proxyFile)
    {
        $proxyList = file($proxyFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        array_walk($proxyList, [$this, 'validateURL']);
        return $proxyList;
    }

    public function initStack()
    {
        if($this->shouldResume()) {
            $this->loadStackFromFile(self::STACK_FILE);
        } else {
            $this->loadHomePage();
        }
        $this->run = true;
    }

    public function shouldResume()
    {
        return file_exists(self::STACK_FILE);
    }

    public function loadStackFromFile($filename)
    {
        $lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach($lines as $line) {
            $values = explode(',', $line);
            $className = array_shift($values);

            if(strstr($className, 'ProfilePage')) {
                $values[0] = substr($values[0], 51);
            }

            array_unshift($values, $this->stack);

            $page = new $className(...$values);
            $this->stack->push($page);
        }
    }

    public function loadHomePage()
    {
        $homepage = new HomePage($this->stack, self::URL, count($this->browsers));
        $this->loadPage($homepage, $this->browsers[0]);
        $this->loop->run();
    }

    public function registerTick($browser)
    {
        $this->loop->addTimer(1, function () use ($browser) {
            if (!$this->stack->isEmpty()) {
                $page = $this->stack->pop();
                $this->loadPage($page, $browser);
            } else {
                $this->activeBrowsers--;
            }
        });
    }

    public function loadPage($page, $browser)
    {
        $this->line(((string) $page) . ' scheduled for ' . (string) $browser);
        $value = $browser->get($page->url)->then(
            function (ResponseInterface $response) use ($page, $browser) {
                if ($this->run) {
                    $this->registerTick($browser);
                }
                $this->processPage($page, $response);
            },
            function ($reason) use ($page, $browser) {
                $this->line(((string) $page) . ' rejected for ' . (string) $browser);
                $this->stack->push($page);
                if ($this->run) {
                    $this->registerTick($browser);
                }
            }
        );
    }

    
    public function processPage($page, $response)
    {
        if ($response->getStatusCode() != 200) {
            $this->stack->push($page);
            return;
        }
        $this->line(((string) $page) . ' loaded');
        $html = (string) $response->getBody();
        $page->setContent($html);
        $addresses = $page->process();
        $this->saveRecords($addresses);
        $this->line(((string) $page) . ' processed');
    }
    
    public function saveRecords($records)
    {
        if($records == NULL) {
            print("no records \n");
            return;
        }

        array_map([$this, 'saveRecord'], $records);
    }
    
    public function saveStackToFile($filename)
    {
        $fd = fopen($filename, 'w');
        $this->stack->rewind();

        while($this->stack->valid()){
            fwrite($fd, (string) $this->stack->current() . PHP_EOL);
            $this->stack->next();
        }

        fclose($fd);
    }
    
    public function sighandler($signo)
    {
        $this->run = false;
        $this->loop->cancelTimer($this->stackDumpTimer);
    }

    
    public function saveRecord($record)
    {
        foreach($record['addresses'] as $address => $coins) {
            foreach($coins as $coin) {
                $owner_db = Owner::firstOrCreate([Owner::COL_NAME => $record['username']]);
                $address_db = Address::firstOrCreate([
                    Address::COL_ADDRESS => $address,
                    Address::COL_CRYPTO => $coin,
                    Address::COL_OWNER => $owner_db->getKey(),
                ]);
                $category = Category::find($record['category']);
                $address_db->categories()->sync($category, $detach=false);
                $identity_db = Identity::firstOrCreate([
                    Identity::COL_SOURCE => 'bitcointalk.org',
                    Identity::COL_LABEL => $record['label'],
                    Identity::COL_URL => $record['url'],
                    Identity::COL_ADDRID => $address_db->getKey(),
                ]);
            }
        }
    }

    public function validateURL($item)
    {
        $result = filter_var($item, FILTER_VALIDATE_URL);

        if (!$result) {
            throw new \Exception("Proxy URL is invalid: {$item}");
        }
    }
}

class DefaultBrowser extends Browser
{
    public function __toString()
    {
        return "default browser";
    }
}

class ProxyBrowser extends Browser
{
    private $proxyUrl;

    public function __construct($loop, $proxyUrl)
    {
        $this->proxyUrl = $proxyUrl;
        $proxy = new HttpConnectClient($proxyUrl, new Connector($loop));
        $connector = new Connector($loop, ['tcp' => $proxy, 'dns' => false]);
        parent::__construct($loop, $connector);
    }

    public function __toString()
    {
        return $this->proxyUrl;
    }
}

abstract class Page
{
    public $url;
    protected $stack;
    protected $crawler;

    const CURRENT_PAGE_XPATH = '(//td[@class="middletext"])[last()]/b[not(contains(text(), " ... ") or contains(text(), "All"))]';

    public function __construct(SplStack $stack, $url)
    {
        $this->stack = $stack;
        $this->url = $url;
    }

    public function setContent($html)
    {
        $this->crawler = new Crawler;
        $this->crawler->addHTMLContent($html, 'UTF-8');
    }

    public function process()
    {
        throw new Exception('Not implemented');
    }

    protected function pushNextPage()
    {
        $nextPages = $this->crawler->filterXPath(self::CURRENT_PAGE_XPATH)->nextAll();

        if (count($nextPages)) {
            $nextPageUrl = $nextPages->first()->attr('href');

            if (strstr($nextPageUrl, ";all")) {
                return;
            }

            $className = get_class($this);
            $nextPage = new $className($this->stack, $nextPageUrl);
            $this->stack->push($nextPage);
        }
    }

    public function __toString()
    {
        return get_class($this) . "," . $this->url;
    }

    public function buildRecord($username, $addresses, $label)
    {
        return [
            'username' => $username,
            'addresses' => $addresses,
            'url' => $this->url,
            'label' => $label,
            'category' => static::RESULT_CATEGORY,
        ];
    }
}

class ProfilePage extends Page
{
    const PROFILE_URL = 'https://bitcointalk.org/index.php?action=profile;u=%s';
    const RESULT_CATEGORY = 2;

    private $lastID;
    private $currentID;

    public function __construct(SplStack $stack, $currentID, $lastID)
    {
        $url = sprintf(self::PROFILE_URL, $currentID);
        parent::__construct($stack, $url);
        $this->currentID = $currentID;
        $this->lastID = $lastID;
    }

    public function process()
    {
        $this->pushNextProfile();
        $title = $this->crawler->filter('title')->text();
        $username = substr($title, strlen('View the profile of '));
        $addresses = AddressMatcher::matchAddresses($this->crawler->html());

        if($addresses) {
            return [$this->buildRecord($username, $addresses, $username)];
        }
    }

    public function pushNextProfile()
    {
        if ($this->currentID <= $this->lastID) {
            $nextProfile = new ProfilePage($this->stack, $this->currentID+1, $this->lastID);
            $this->stack->push($nextProfile);
        }
    }


    public function __toString()
    {
        return get_class($this) . "," . $this->url . "," . $this->lastID;
    }
}

class TopicPage extends Page
{
    const RESULT_CATEGORY = 1;

    public function process()
    {
        $this->pushNextPage();
        $results = $this->matchAddresses();
        return $results;
    }

    private function matchAddresses()
    {
        $title = $this->crawler->filter('title')->text();
        // td_headerandpost - only text of reply - user's signatures are processed in profiles
        $results = $this->crawler->filter('.td_headerandpost')->each(function ($node) use($title) {
            $addresses = AddressMatcher::matchAddresses($node->html());
            $userInfo = $node->previousAll()->first();
            $userName = $userInfo->filter('a')->first()->text();
            $msgURL = $node->filter('a')->first()->attr('href');

            if($addresses) {
                return $this->buildRecord($userName, $addresses, $title);
            }
        });

        return array_filter($results); // filter out replies without addresses
    }
}

class BoardPage extends Page
{

    public function process()
    {
        $this->pushNextPage();
        $this->pushTopics();
    }

    public function pushTopics()
    {
        $this->crawler->filter('span[id*=msg_] > a')->each(function ($node) {
            $topicUrl = $node->attr('href');
            $topicPage = new TopicPage($this->stack, $topicUrl);
            $this->stack->push($topicPage);
        });
    }
}

class HomePage extends Page
{
    public function __construct(\SplStack $stack, $url, $profileChunkCount)
    {
        parent::__construct($stack, $url);
        $this->profileChunkCount = $profileChunkCount;
    }

    public function process()
    {
        $this->pushProfiles();
        $this->pushBoards();
    }

    public function pushBoards()
    {
        $this->crawler->filter('a[href*=board]')->each(function ($node) {
            $boardUrl = $node->attr('href');

            if (strstr($boardUrl, 'unread')) {
                return;
            }

            $boardPage = new BoardPage($this->stack, $boardUrl);
            $this->stack->push($boardPage);
        });
    }

    public function pushProfiles()
    {
        $lastID = $this->getLastMemberID();
        $step = intval($lastID / $this->profileChunkCount);
        $startID = 1;

        while($startID < $lastID) {
            $endID = $startID + $step;
            $profilePage = new ProfilePage($this->stack, $startID, min($endID, $lastID));
            $this->stack->push($profilePage);
            $startID = $endID + 1;
        }
    }

    public function getLastMemberID()
    {
        $url = $this->crawler->filter('a[href*=profile]')->last()->attr('href');
        list($_, $_, $id) = explode('=', $url);
        return intval($id);
    }
}
