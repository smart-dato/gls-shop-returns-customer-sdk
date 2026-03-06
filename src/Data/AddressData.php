<?php

namespace SmartDato\GlsShopReturnsCustomer\Data;

use Spatie\LaravelData\Data;

class AddressData extends Data
{
    public function __construct(
        public string $street,
        public string $city,
        public string $zipCode,
        public string $countryCode,
    ) {}
}
