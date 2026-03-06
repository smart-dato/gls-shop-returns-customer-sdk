<?php

namespace SmartDato\GlsShopReturnsCustomer\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use SmartDato\GlsShopReturnsCustomer\Enums\LabelFormat;
use SmartDato\GlsShopReturnsCustomer\Enums\LabelType;

class GetReturnOrderRawLabelRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected string $returnOrderId,
        protected LabelType $labelType,
        protected LabelFormat $labelFormat,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/return-orders/{$this->returnOrderId}/{$this->labelType->value}/{$this->labelFormat->value}";
    }

    protected function defaultHeaders(): array
    {
        return [
            'Accept' => match ($this->labelFormat) {
                LabelFormat::Pdf => 'application/pdf',
                LabelFormat::Png => 'image/png',
                LabelFormat::Zpl => 'text/plain',
            },
        ];
    }
}
