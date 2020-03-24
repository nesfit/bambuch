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
        $addressInfo = Address::getByAddress($address);
        
        if ($addressInfo) {
            $identities = $addressInfo->identities()->get()->all();

            $identityData = array_map(function ($item) { return new IdentityView($item); }, $identities);
            $addressData = new AddressView($addressInfo);

            return view('address', [
                'identities' => $identityData,
                'address' => $addressData,
                'searchValue' => $address
            ]);
        }
        return view('nothing-found', [
            'searchValue' => $address,
            'searchRoute' => 'address'
        ]);
    }
}
