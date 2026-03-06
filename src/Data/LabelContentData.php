<?php

namespace SmartDato\GlsShopReturnsCustomer\Data;

use Spatie\LaravelData\Data;

class LabelContentData extends Data
{
    public function __construct(
        public string $contentType,
        public string $content,
    ) {}
}
