<?php

namespace SmartDato\GlsShopReturnsCustomer\Data;

use SmartDato\GlsShopReturnsCustomer\Enums\LabelLayout;
use Spatie\LaravelData\Data;

class LabelOptionsData extends Data
{
    public function __construct(
        public ?LabelLayout $layout = null,
        public ?ConsigneeData $consignee = null,
    ) {}
}
