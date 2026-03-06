<?php

namespace SmartDato\GlsShopReturnsCustomer\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;
use SmartDato\GlsShopReturnsCustomer\Data\CreateReturnOrderData;
use SmartDato\GlsShopReturnsCustomer\Data\ReturnOrderWithLabelData;
use SmartDato\GlsShopReturnsCustomer\Enums\LabelDpi;
use SmartDato\GlsShopReturnsCustomer\Enums\LabelFormat;

class CreateReturnOrderWithLabelRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $appId,
        protected CreateReturnOrderData $data,
        protected ?LabelFormat $labelFormat = null,
        protected ?LabelDpi $labelDpi = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/{$this->appId}/return-orders/label";
    }

    protected function defaultBody(): array
    {
        return $this->data->toArray();
    }

    protected function defaultQuery(): array
    {
        return array_filter([
            'labelFormat' => $this->labelFormat?->value,
            'labelDpi' => $this->labelDpi?->value,
        ]);
    }

    public function createDtoFromResponse(Response $response): ReturnOrderWithLabelData
    {
        return ReturnOrderWithLabelData::from($response->json());
    }
}
