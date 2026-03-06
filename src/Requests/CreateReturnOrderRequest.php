<?php

namespace SmartDato\GlsShopReturnsCustomer\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;
use SmartDato\GlsShopReturnsCustomer\Data\CreateReturnOrderData;
use SmartDato\GlsShopReturnsCustomer\Data\ReturnOrderData;

class CreateReturnOrderRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $appId,
        protected CreateReturnOrderData $data,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/{$this->appId}/return-orders";
    }

    protected function defaultBody(): array
    {
        return $this->data->toArray();
    }

    public function createDtoFromResponse(Response $response): ReturnOrderData
    {
        return ReturnOrderData::from($response->json());
    }
}
