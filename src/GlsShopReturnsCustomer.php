<?php

namespace SmartDato\GlsShopReturnsCustomer;

use SmartDato\GlsShopReturnsCustomer\Connectors\GlsShopReturnsConnector;
use SmartDato\GlsShopReturnsCustomer\Data\CreateReturnOrderData;
use SmartDato\GlsShopReturnsCustomer\Data\ReturnOrderData;
use SmartDato\GlsShopReturnsCustomer\Data\ReturnOrderWithLabelData;
use SmartDato\GlsShopReturnsCustomer\Data\ReturnOrderWithQrCodeData;
use SmartDato\GlsShopReturnsCustomer\Enums\LabelDpi;
use SmartDato\GlsShopReturnsCustomer\Enums\LabelFormat;
use SmartDato\GlsShopReturnsCustomer\Enums\LabelType;
use SmartDato\GlsShopReturnsCustomer\Requests\CreateReturnOrderRequest;
use SmartDato\GlsShopReturnsCustomer\Requests\CreateReturnOrderWithLabelRequest;
use SmartDato\GlsShopReturnsCustomer\Requests\CreateReturnOrderWithQrCodeRequest;
use SmartDato\GlsShopReturnsCustomer\Requests\CreateReturnOrderWithRawLabelRequest;
use SmartDato\GlsShopReturnsCustomer\Requests\CreateReturnOrderWithRawQrCodeRequest;
use SmartDato\GlsShopReturnsCustomer\Requests\GetReturnOrderLabelRequest;
use SmartDato\GlsShopReturnsCustomer\Requests\GetReturnOrderRawLabelRequest;

class GlsShopReturnsCustomer
{
    public function __construct(
        protected GlsShopReturnsConnector $connector,
        protected string $appId,
    ) {}

    public function createReturnOrder(CreateReturnOrderData $data): ReturnOrderData
    {
        $this->connector->refreshAuthenticator();

        $response = $this->connector->send(new CreateReturnOrderRequest($this->appId, $data));

        $response->throw();

        return $response->dto();
    }

    public function createReturnOrderWithLabel(
        CreateReturnOrderData $data,
        ?LabelFormat $labelFormat = null,
        ?LabelDpi $labelDpi = null,
    ): ReturnOrderWithLabelData {
        $this->connector->refreshAuthenticator();

        $response = $this->connector->send(new CreateReturnOrderWithLabelRequest($this->appId, $data, $labelFormat, $labelDpi));

        $response->throw();

        return $response->dto();
    }

    public function createReturnOrderWithRawLabel(
        CreateReturnOrderData $data,
        LabelFormat $labelFormat,
        ?LabelDpi $labelDpi = null,
    ): string {
        $this->connector->refreshAuthenticator();

        $response = $this->connector->send(new CreateReturnOrderWithRawLabelRequest($this->appId, $data, $labelFormat, $labelDpi));

        $response->throw();

        return $response->body();
    }

    public function createReturnOrderWithQrCode(
        CreateReturnOrderData $data,
        ?LabelDpi $labelDpi = null,
    ): ReturnOrderWithQrCodeData {
        $this->connector->refreshAuthenticator();

        $response = $this->connector->send(new CreateReturnOrderWithQrCodeRequest($this->appId, $data, $labelDpi));

        $response->throw();

        return $response->dto();
    }

    public function createReturnOrderWithRawQrCode(
        CreateReturnOrderData $data,
        LabelFormat $labelFormat,
        ?LabelDpi $labelDpi = null,
    ): string {
        $this->connector->refreshAuthenticator();

        $response = $this->connector->send(new CreateReturnOrderWithRawQrCodeRequest($this->appId, $data, $labelFormat, $labelDpi));

        $response->throw();

        return $response->body();
    }

    public function getLabel(
        string $returnOrderId,
        LabelType $labelType = LabelType::Label,
        ?LabelFormat $labelFormat = null,
        ?LabelDpi $labelDpi = null,
    ): ReturnOrderWithLabelData|ReturnOrderWithQrCodeData {
        $this->connector->refreshAuthenticator();

        $response = $this->connector->send(new GetReturnOrderLabelRequest($returnOrderId, $labelType, $labelFormat, $labelDpi));

        $response->throw();

        return $response->dto();
    }

    public function getRawLabel(
        string $returnOrderId,
        LabelType $labelType,
        LabelFormat $labelFormat,
    ): string {
        $this->connector->refreshAuthenticator();

        $response = $this->connector->send(new GetReturnOrderRawLabelRequest($returnOrderId, $labelType, $labelFormat));

        $response->throw();

        return $response->body();
    }

    public function connector(): GlsShopReturnsConnector
    {
        return $this->connector;
    }
}
