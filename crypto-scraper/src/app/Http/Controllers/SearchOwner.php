<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Pg\Owner;
use App\Models\Views\AddressView;
use App\Models\Views\OwnerView;
use Illuminate\Support\Facades\Request;

class SearchOwner extends Controller {

    public function __invoke() {
        $ownerName = Request::input('search');
        if (!$ownerName) {
            $owners = Owner::query()->limit(30)->get()->all();
            $ownersData = array_map(function ($item) { return new OwnerView($item); }, $owners);

            return view('intro.owner', [
                'searchType' => 'owners',
                'owners' => $ownersData
            ]);
        }
        
        $ownerInfo = Owner::getByName($ownerName);
        if (!$ownerInfo) {
            return view('nothing-found', [
                'searchValue' => $ownerName,
                'searchRoute' => 'owner'
            ]);
            
        }

        $addresses = $ownerInfo->addresses()->limit(20)->get()->all();
        $addressData = array_map(function ($item) { return new AddressView($item); }, $addresses);
        $ownerData = new OwnerView($ownerInfo);

        return view('result.owner', [
            'addresses' => $addressData,
            'owner' => $ownerData,
            'searchValue' => $ownerName
        ]);
    }
}


