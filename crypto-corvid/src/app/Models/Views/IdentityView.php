<?php
declare(strict_types=1);

namespace App\Models\Views;

use App\Models\Pg\Identity;

class IdentityView extends BaseView {
    public string $source;
    public int $id;
    public string $url;
    public string $label;
    public string $created;
    public string $updated;

    public function __construct(Identity $identity) {
        $this->source = parse_url($identity->getAttribute(Identity::COL_SOURCE))['host'];
        $this->id = 1;
        $this->url = $identity->getAttribute(Identity::COL_URL);
        $this->label = $identity->getAttribute(Identity::COL_LABEL);
        $this->created = $identity->getAttribute(Identity::COL_CREATEDAT)->format(self::FORMAT_MINUTES);
        $this->updated = $identity->getAttribute(Identity::COL_UPDATEDAT)->format(self::FORMAT_MINUTES);
    }
}