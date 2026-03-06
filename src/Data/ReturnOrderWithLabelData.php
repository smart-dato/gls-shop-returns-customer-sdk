<?php

namespace SmartDato\GlsShopReturnsCustomer\Data;

use Spatie\LaravelData\Data;

class ReturnOrderWithLabelData extends Data
{
    public function __construct(
        public string $returnOrderId,
        public LabelContentData $label,
        public ?ReferencesData $references = null,
        public ?NationalReferencesData $nationalReferences = null,
    ) {}
}
