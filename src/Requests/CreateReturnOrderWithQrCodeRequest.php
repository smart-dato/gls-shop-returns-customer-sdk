<?php

namespace SmartDato\GlsShopReturnsCustomer\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;
use SmartDato\GlsShopReturnsCustomer\Data\CreateReturnOrderData;
use SmartDato\GlsShopReturnsCustomer\Data\ReturnOrderWithQrCodeData;
use SmartDato\GlsShopReturnsCustomer\Enums\LabelDpi;

class CreateReturnOrderWithQrCodeRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $appId,
        protected CreateReturnOrderData $data,
        protected ?LabelDpi $labelDpi = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/{$this->appId}/return-orders/parcelshop-qrcode";
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

    public function createDtoFromResponse(Response $response): ReturnOrderWithQrCodeData
    {
        return ReturnOrderWithQrCodeData::from($response->json());
    }
}
