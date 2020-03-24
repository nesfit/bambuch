<?php
declare(strict_types=1);

namespace App\Models\Views;

use App\Models\Pg\Category;
use App\Models\Pg\Owner;

class OwnerView extends BaseView {
    public string $category;
    public string $name;
    public string $created;
    public string $updated;

    public function __construct(Owner $ownerData) {
        $this->category = Category::CAT_2; // TODO FIX get actual category from DB -> add it into DB schema
        $this->name = $ownerData->getAttribute(Owner::COL_NAME);
        $this->created = $ownerData->getAttribute(Owner::COL_CREATEDAT)->format(self::FORMAT_MINUTES);
        $this->updated = $ownerData->getAttribute(Owner::COL_UPDATEDAT)->format(self::FORMAT_MINUTES);
    }
}