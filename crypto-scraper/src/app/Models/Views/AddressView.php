<?php
declare(strict_types=1);

namespace App\Models\Views;

use App\Models\Pg\Address;
use App\Models\Pg\Category;
use App\Models\Pg\Owner;
use Carbon\Carbon;

class AddressView extends BaseView {
    public string $owner;
    public int $currency;
    public string $category;
    public string $created;
    public string $updated;
    public string $address;

    public function __construct(Address $addressData) {
        $owner = $addressData->owner()->first();
        $currency = $addressData->getAttribute(Address::COL_CRYPTO); // TODO FIX to display actual currency name NOT INTEGER
        $category = $addressData->categories()->first()->getAttribute(Category::COL_NAME); // TODO FIX display all categories

        $this->owner = $owner ? $owner->getAttribute(Owner::COL_NAME) : 'unknown_owner';
        $this->currency = $currency;
        $this->category = $category;
        $this->address = $addressData->getAttribute(Address::COL_ADDRESS);
        $this->created = $addressData->getAttribute(Address::COL_CREATEDAT)->format(self::FORMAT_MINUTES);
        $this->updated = $addressData->getAttribute(Address::COL_UPDATEDAT)->format(self::FORMAT_MINUTES);
    }
}