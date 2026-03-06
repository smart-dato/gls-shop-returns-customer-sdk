<?php

namespace SmartDato\GlsShopReturnsCustomer\Data;

use Spatie\LaravelData\Data;

class ConsigneeData extends Data
{
    public function __construct(
        public ConsigneeInfoData $info,
    ) {}
}
