<?php

namespace App\Console\Commands;

use App\Models\Pg\Address;
use App\Models\Pg\Category;
use App\Models\Pg\Identity;
use App\Models\Pg\Owner;
use App\Models\Pg\WalletExplorer;
use Illuminate\Console\Command;

class InsertIntoDB extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insert:db {owner name} {url} {label} {source} {address} {crypto type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert address to DB';

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
        $ownerName = $this->argument('owner name');
        $url = $this->argument('url');
        $label = $this->argument('label');
        $source = $this->argument('source');
        $address = $this->argument('address');
        $cryptoType = $this->argument('crypto type');

        $owner = Owner::getByName($ownerName);
        $category = $this->getCategory($ownerName);
        $existingAddress = Address::getByAddress($address);
        
        if ($existingAddress == null) { // no address in the database
            $identity = $this->getNewIdentity($source, $url, $label);

            $ownerAddr = new Address();
            $ownerAddr->address = $address;
            $ownerAddr->crypto = $cryptoType;
            $ownerAddr->color = $category->color;
            $ownerAddr->save();
            $ownerAddr->identities()->save($identity);
            $ownerAddr->categories()->attach($category->id);

            $owner->addresses()->save($ownerAddr);
            return 1;
        } else if ($this->newIdentity($existingAddress->id, $source)) {
            // no identity for the address in the database
            $identity = $this->getNewIdentity($source, $url, $label);
            $existingAddress->identities()->save($identity);
        }
        return 0;
    }
    
    /**
     * Get specific category from `Category` class based on owner name.
     *
     * @param string $ownerName
     * @return Category
     */
    private function getCategory(string $ownerName) {
        $owner = WalletExplorer::getByOwnerLike($ownerName);
        if ($owner) {
            return Category::getByName($owner->category);
        }
        return Category::getByName(Category::CAT_1);
    }

    /**
     * Checks if there is already an identity for specific combiantion of cryptoaddress and source url.
     * Enables adding new identities for existing cryptoaddress.
     *
     * @param string $addr_id Address id
     * @param string $newSource Source url of potential new identity
     * @return bool
     */
    private function newIdentity($addr_id, $newSource) {
        $identities = Identity::getIdentitiesByAddress($addr_id);
        $existingIdentities = $identities->reduce(function ($acc, $identity) {
            array_push($acc, $identity->source);
            return $acc;
        }, []);
        return in_array($newSource, $existingIdentities) == false;
    }

    private function getNewIdentity($source, $url, $label) {
        $identity = new Identity();
        $identity->source = $source;
        $identity->url = $url;
        $identity->label = $label;
        $identity->save();
        return $identity;
    }

}
