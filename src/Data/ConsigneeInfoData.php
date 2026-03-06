<?php

namespace SmartDato\GlsShopReturnsCustomer\Data;

use Spatie\LaravelData\Data;

class ConsigneeInfoData extends Data
{
    public function __construct(
        public string $type,
        public ConsigneeInfoArgsData $args,
    ) {}
}
