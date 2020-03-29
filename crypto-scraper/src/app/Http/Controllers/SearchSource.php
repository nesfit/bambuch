<?php

namespace App\Http\Controllers;

use App\Models\Pg\Source;
use App\Models\Views\AddressView;
use App\Models\Views\SourceView;
use Illuminate\Support\Facades\Request;

class SearchSource extends Controller {
    
    public function __invoke() {

        $source = Request::input('search');
        if (!$source) {
            $source = Source::all()->all();
            $sourceData = array_map(function ($item) { return new SourceView($item); }, $source);

            return view('intro.source', [
                'searchType' => 'sources',
                'sources' => $sourceData
            ]);
        }

        $sourceInfo = Source::getByName($source);
        if (!$sourceInfo) {
            return view('nothing-found', [
                'searchValue' => $source,
                'searchRoute' => 'source'
            ]);
        }

        $addresses = Source::getAddresses($sourceInfo->getAttribute(Source::COL_URL));

        $addressesData = array_map(function ($item) { return new AddressView($item); }, $addresses);
        $sourceData = new SourceView($sourceInfo);
        
        return view('result.source', [
            'addresses' => $addressesData,
            'source' => $sourceData,
            'searchValue' => $source
        ]);
    }
}
