<?php

namespace SmartDato\GlsShopReturnsCustomer\Data;

use Spatie\LaravelData\Data;

class ErrorData extends Data
{
    public function __construct(
        public ?string $type = null,
        public ?string $fieldName = null,
        public ?string $fieldValue = null,
        public ?string $message = null,
        public ?string $documentationUrl = null,
    ) {}
}
