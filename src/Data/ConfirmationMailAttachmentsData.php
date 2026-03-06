<?php

namespace SmartDato\GlsShopReturnsCustomer\Data;

use Spatie\LaravelData\Data;

class ConfirmationMailAttachmentsData extends Data
{
    public function __construct(
        /** @var array{include?: bool}|null */
        public ?array $label = null,
        /** @var array{include?: bool}|null */
        public ?array $parcelShopQrCode = null,
    ) {}
}
