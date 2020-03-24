<?php

namespace App\Http\Controllers;

use App\Models\Pg\Address;
use App\Models\Pg\Category;
use App\Models\Pg\Identity;
use App\Models\Pg\Owner;
use Illuminate\Support\Facades\Request;

class SearchAddress extends Controller {
    
    
    public function get() {
        $address = Request::input('search');
        $addressInfo = Address::getByAddress($address);
        
        if ($addressInfo) {
            $identities = $addressInfo->identities()->get()->all();

            $addressData = new AddressView($addressInfo);
            $identityData = array_map(function ($item) { return new IdentityView($item); }, $identities);

            return view('address',
                [
                    'identities' => $identityData,
                    'address' => $addressData,
                    'searchValue' => $address
                ]);
        }
        return view('nothing-found', ['searchValue' => $address]);
    }
}

class IdentityView {
    public string $source;
    public string $url;
    public string $label;
    public string $created;
    public string $updated;
    
    public function __construct(Identity $identity) {
        $this->source = parse_url($identity->getAttribute(Identity::COL_SOURCE))['host'];
        $this->url = $identity->getAttribute(Identity::COL_URL);
        $this->label = $identity->getAttribute(Identity::COL_LABEL);
        $this->created = $identity->getAttribute(Identity::COL_CREATEDAT);
        $this->updated = $identity->getAttribute(Identity::COL_UPDATEDAT);
    }
}

class AddressView {
    public string $owner;
    public string $currency;
    public string $category;
    
    public function __construct(Address $address) {
        $owner = $address->owner()->first();
        $currency = $address->getAttribute(Address::COL_CRYPTO); // TODO FIX to display actual currency name NOT INTEGER
        $category = $address->categories()->first()->getAttribute(Category::COL_NAME); // TODO FIX display all categories
        
        $this->owner = $owner->getAttribute(Owner::COL_NAME);
        $this->currency = $currency;
        $this->category = $category;
    }
}