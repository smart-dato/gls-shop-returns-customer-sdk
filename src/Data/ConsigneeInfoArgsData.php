<?php

namespace SmartDato\GlsShopReturnsCustomer\Data;

use Spatie\LaravelData\Data;

class ConsigneeInfoArgsData extends Data
{
    public function __construct(
        public string $value,
        public ?string $line1 = null,
        public ?string $line2 = null,
        public ?string $line3 = null,
        public ?string $line4 = null,
    ) {}
}
