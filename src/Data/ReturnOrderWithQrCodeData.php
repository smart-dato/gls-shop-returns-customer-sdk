<?php

namespace SmartDato\GlsShopReturnsCustomer\Data;

use Spatie\LaravelData\Data;

class ReturnOrderWithQrCodeData extends Data
{
    public function __construct(
        public string $returnOrderId,
        public LabelContentData $parcelShopQrCode,
        public ?ReferencesData $references = null,
        public ?NationalReferencesData $nationalReferences = null,
    ) {}
}
