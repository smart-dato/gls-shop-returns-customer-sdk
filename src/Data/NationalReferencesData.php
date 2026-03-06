<?php

namespace SmartDato\GlsShopReturnsCustomer\Data;

use Spatie\LaravelData\Data;

class NationalReferencesData extends Data
{
    public function __construct(
        public ?string $colloId = null,
    ) {}
}
