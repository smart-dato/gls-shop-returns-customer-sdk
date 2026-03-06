<?php

namespace SmartDato\GlsShopReturnsCustomer\Data;

use SmartDato\GlsShopReturnsCustomer\Enums\LanguageCode;
use Spatie\LaravelData\Data;

class OptionsData extends Data
{
    public function __construct(
        public ?ConfirmationMailData $confirmationMail = null,
        public ?LanguageCode $languageCode = null,
        public ?LabelOptionsData $label = null,
    ) {}
}
