<?php

namespace App\Console\Commands;

use App\Console\Base\Utils;
use App\Models\Pg\Category;
use App\Models\Pg\WalletExplorer;
use Illuminate\Console\Command;

class FetchWalletExplorer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallet-explorer';

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
    public function handle()
    {
        $walletEContent = Utils::getContentFromURL("https://www.walletexplorer.com");;
        $walletExplorerXPath = Utils::getDOMXPath($walletEContent);
        if ($walletExplorerXPath == "") {
            $this->error("WalletExplorer fetching failed...ending.");
            exit();
        }
        
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
                $this->alert("Unknown category");
                print_r($match);
            }
        }
    }
    
    private function insertCategory(string $category, string $owner) {
        if (WalletExplorer::getByOwner($owner) == null) {
            $walletItem = new WalletExplorer();
            $walletItem->category = $category;
            $walletItem->owner = $owner;
            $walletItem->save();
        }
    }
}
