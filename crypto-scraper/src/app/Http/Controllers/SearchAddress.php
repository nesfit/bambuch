<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Pg\Address;
use App\Models\Views\AddressView;
use App\Models\Views\IdentityView;
use Illuminate\Support\Facades\Request;

class SearchAddress extends Controller {
    
    public function get() {
        
        $address = Request::input('search');
        if (!$address) {
            $address = Address::query()->limit(30)->get()->all();
            $addressData = array_map(function ($item) { return new AddressView($item); }, $address);

            return view('address-intro', [
                'searchType' => 'addresses',
                'addresses' => $addressData
            ]);
        }        
        
        $addressInfo = Address::getByAddress($address);
        if (!$addressInfo) {
            return view('nothing-found', [
                'searchValue' => $address,
                'searchRoute' => 'address'
            ]);
        }

        $identities = $addressInfo->identities()->get()->all();

        $identityData = array_map(function ($item) { return new IdentityView($item); }, $identities);
        $addressData = new AddressView($addressInfo);

        return view('address-result', [
            'identities' => $identityData,
            'address' => $addressData,
            'searchValue' => $address
        ]);
    }
}
