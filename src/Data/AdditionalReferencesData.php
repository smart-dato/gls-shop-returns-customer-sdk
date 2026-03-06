<?php

namespace SmartDato\GlsShopReturnsCustomer\Data;

use Spatie\LaravelData\Data;

class AdditionalReferencesData extends Data
{
    public function __construct(
        public ?string $broker = null,
        public ?string $nationalContactId = null,
    ) {}
}
