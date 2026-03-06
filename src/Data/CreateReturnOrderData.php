<?php

namespace SmartDato\GlsShopReturnsCustomer\Data;

use Spatie\LaravelData\Data;

class CreateReturnOrderData extends Data
{
    public function __construct(
        public string $originalOrderReference,
        public string $returnReason,
        public SenderData $sender,
        public ?ParcelData $parcel = null,
        public ?OptionsData $options = null,
        public ?AdditionalReferencesData $additionalReferences = null,
    ) {}
}
