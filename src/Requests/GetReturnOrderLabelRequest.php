<?php

namespace SmartDato\GlsShopReturnsCustomer\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use SmartDato\GlsShopReturnsCustomer\Data\ReturnOrderWithLabelData;
use SmartDato\GlsShopReturnsCustomer\Data\ReturnOrderWithQrCodeData;
use SmartDato\GlsShopReturnsCustomer\Enums\LabelDpi;
use SmartDato\GlsShopReturnsCustomer\Enums\LabelFormat;
use SmartDato\GlsShopReturnsCustomer\Enums\LabelType;

class GetReturnOrderLabelRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected string $returnOrderId,
        protected LabelType $labelType,
        protected ?LabelFormat $labelFormat = null,
        protected ?LabelDpi $labelDpi = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/return-orders/{$this->returnOrderId}/{$this->labelType->value}";
    }

    protected function defaultQuery(): array
    {
        return array_filter([
            'labelFormat' => $this->labelFormat?->value,
            'labelDpi' => $this->labelDpi?->value,
        ]);
    }

    public function createDtoFromResponse(Response $response): ReturnOrderWithLabelData|ReturnOrderWithQrCodeData
    {
        $json = $response->json();

        if ($this->labelType === LabelType::ParcelShopQrCode) {
            return ReturnOrderWithQrCodeData::from($json);
        }

        return ReturnOrderWithLabelData::from($json);
    }
}
