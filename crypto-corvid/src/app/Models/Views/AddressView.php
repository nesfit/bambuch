<?php
declare(strict_types=1);

namespace App\Models\Views;

use App\Console\Constants\Common\CryptoCurrency;
use App\Models\Pg\Address;
use App\Models\Pg\Category;
use App\Models\Pg\Owner;

class AddressView extends BaseView {
    public string $owner;
    public string $currency;
    public string $category;
    public string $created;
    public string $updated;
    public string $address;

    public function __construct(Address $addressData) {
        $owner = $addressData->owner()->first();
        $currency = $addressData->getAttribute(Address::COL_CRYPTO); // TODO FIX to display actual currency name NOT INTEGER
        $firstCategory = $addressData->categories()->first(); // TODO FIX display all categories
        $category = $firstCategory ? $firstCategory->getAttribute(Category::COL_NAME) : ''; 

        $this->owner = $owner ? $owner->getAttribute(Owner::COL_NAME) : 'unknown_owner';
        $this->currency = CryptoCurrency::getShortcut($currency);
        $this->category = $category;
        $this->address = $addressData->getAttribute(Address::COL_ADDRESS);
        $this->created = $addressData->getAttribute(Address::COL_CREATEDAT)->format(self::FORMAT_MINUTES);
        $this->updated = $addressData->getAttribute(Address::COL_UPDATEDAT)->format(self::FORMAT_MINUTES);
    }
}