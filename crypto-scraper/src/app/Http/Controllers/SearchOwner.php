<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Pg\Owner;
use App\Models\Views\AddressView;
use App\Models\Views\OwnerView;
use Illuminate\Support\Facades\Request;

class SearchOwner extends Controller {
    
    public function get() {
        $ownerName = Request::input('search');
        
        $ownerInfo = Owner::getByName($ownerName);
        
        if ($ownerInfo) {
            $addresses = $ownerInfo->addresses()->limit(20)->get()->all();

            $addressData = array_map(function ($item) { return new AddressView($item); }, $addresses);
            $ownerData = new OwnerView($ownerInfo);
            
            return view('owner', [
                'addresses' => $addressData,
                'owner' => $ownerData,
                'searchValue' => $ownerName
            ]);
        }
        return view('nothing-found', [
            'searchValue' => $ownerName,
            'searchRoute' => 'owner'
        ]);
    }
}


