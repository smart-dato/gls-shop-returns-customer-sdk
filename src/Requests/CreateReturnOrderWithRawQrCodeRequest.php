<?php

namespace SmartDato\GlsShopReturnsCustomer\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;
use SmartDato\GlsShopReturnsCustomer\Data\CreateReturnOrderData;
use SmartDato\GlsShopReturnsCustomer\Enums\LabelDpi;
use SmartDato\GlsShopReturnsCustomer\Enums\LabelFormat;

class CreateReturnOrderWithRawQrCodeRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $appId,
        protected CreateReturnOrderData $data,
        protected LabelFormat $labelFormat,
        protected ?LabelDpi $labelDpi = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/{$this->appId}/return-orders/parcelshop-qrcode/{$this->labelFormat->value}";
    }

    protected function defaultBody(): array
    {
        return $this->data->toArray();
    }

    protected function defaultQuery(): array
    {
        return array_filter([
            'labelDpi' => $this->labelDpi?->value,
        ]);
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
