<?php

namespace SmartDato\GlsShopReturnsCustomer\Data;

use Spatie\LaravelData\Data;

class ConfirmationMailData extends Data
{
    public function __construct(
        /** @var string[] */
        public array $sendTo,
        public ?ConfirmationMailAttachmentsData $attachments = null,
    ) {}
}
