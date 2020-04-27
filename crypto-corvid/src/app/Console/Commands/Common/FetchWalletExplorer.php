<?php
declare(strict_types=1);

namespace App\Console\Commands\Common;

use App\Console\Base\Common\CryptoParser;
use App\Console\Base\Common\GraylogTypes;
use App\Console\Base\Common\Utils;
use App\Console\Constants\Common\CommonCommands;
use App\Models\Pg\Category;
use App\Models\Pg\WalletExplorer;

class FetchWalletExplorer extends CryptoParser
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = CommonCommands::FETCH_WALLET_EXPLORER . '{verbose=1} {--force} {dateTime?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch WalletExplorer data into DB';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $this->serviceName = CommonCommands::FETCH_WALLET_EXPLORER;
        
        $walletEContent = Utils::getContentFromURL("https://www.walletexplorer.com");;
        $walletExplorerXPath = Utils::getDOMXPath($walletEContent);
        if ($walletExplorerXPath == "") {
            $this->errorGraylog("WalletExplorer fetching failed...ending.");
            exit();
        }
        
        $this->infoGraylog("Going to insert categories", GraylogTypes::INFO);
        $this->insertCategories($walletExplorerXPath,"Exchanges", Category::CAT_3);
        $this->insertCategories($walletExplorerXPath,"Pools", Category::CAT_6);
        $this->insertCategories($walletExplorerXPath,"Services/others", Category::CAT_1);
        $this->insertCategories($walletExplorerXPath,"Gambling", Category::CAT_8);
        
        return true;
    }
    
    private function insertCategories(\DOMXPath $walletExplorerXPath, string $label, string $category) {
        $response = $walletExplorerXPath->query("//text()[contains(.,'". $label .":')]/../../ul");
        $childNodes = $response->item(0)->childNodes;
        foreach ($childNodes as $item) {
            preg_match("/[\w.-]+/", $item->nodeValue,$match);
            if (count($match)) {
                $lowerCaseOwner = strtolower($match[0]);
                $this->insertCategory($category, $lowerCaseOwner);
            } else {
                $this->warningGraylog("Unknown category", $category, ["match" => $match]);
            }
        }
    }
    
    private function insertCategory(string $category, string $owner) {
        if (WalletExplorer::getByOwner($owner) == null) {
            $walletItem = new WalletExplorer();
            $walletItem->category = $category;
            $walletItem->owner = $owner;
            $walletItem->save();
            
            $this->infoGraylog("Category inserted", GraylogTypes::SUCCESS);
        } else {
            $this->debugGraylog("Category already exists", GraylogTypes::INFO);
        }
    }
}
