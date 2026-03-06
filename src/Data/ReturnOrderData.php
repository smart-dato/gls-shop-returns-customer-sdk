<?php

namespace SmartDato\GlsShopReturnsCustomer\Data;

use Spatie\LaravelData\Data;

class ReturnOrderData extends Data
{
    public function __construct(
        public string $returnOrderId,
        public ?ReferencesData $references = null,
        public ?NationalReferencesData $nationalReferences = null,
    ) {}
}
