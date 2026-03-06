<?php

namespace SmartDato\GlsShopReturnsCustomer\Data;

use Spatie\LaravelData\Data;

class ReferencesData extends Data
{
    public function __construct(
        public ?string $trackId = null,
        public ?string $parcelId = null,
    ) {}
}
