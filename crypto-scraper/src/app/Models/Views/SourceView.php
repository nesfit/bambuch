<?php
declare(strict_types=1);

namespace App\Models\Views;

use App\Models\Pg\Source;

class SourceView extends BaseView {
    public string $name;
    public string $url;

    public function __construct(Source $sourceData) {
        $this->name = $sourceData->getAttribute(Source::COL_NAME);
        $this->url = $sourceData->getAttribute(Source::COL_URL);
    }
}