<?php

namespace SmartDato\GlsShopReturnsCustomer\Data;

use Spatie\LaravelData\Data;

class SenderData extends Data
{
    public function __construct(
        public string $personName,
        public AddressData $address,
        public ?string $companyName = null,
        public ?string $email = null,
    ) {}
}
