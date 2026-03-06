<?php

namespace SmartDato\GlsShopReturnsCustomer\Data;

use Spatie\LaravelData\Data;

class ParcelData extends Data
{
    public function __construct(
        public ?float $weight = null,
    ) {}
}
