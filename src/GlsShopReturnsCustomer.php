<?php

namespace SmartDato\GlsShopReturnsCustomer;

use Saloon\Http\Response;
use SmartDato\GlsShopReturnsCustomer\Auth\GlsAuthenticator;
use SmartDato\GlsShopReturnsCustomer\Connectors\GlsShopReturnsConnector;
use SmartDato\GlsShopReturnsCustomer\Data\CreateReturnOrderData;
use SmartDato\GlsShopReturnsCustomer\Data\ReturnOrderData;
use SmartDato\GlsShopReturnsCustomer\Data\ReturnOrderWithLabelData;
use SmartDato\GlsShopReturnsCustomer\Data\ReturnOrderWithQrCodeData;
use SmartDato\GlsShopReturnsCustomer\Enums\Environment;
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
    protected ?Response $lastResponse = null;

    public function __construct(
        protected GlsShopReturnsConnector $connector,
        protected string $appId,
    ) {}

    /**
     * Build an instance manually without the service container.
     *
     * @param  array{
     *     client_id: string,
     *     client_secret: string,
     *     app_id: string,
     *     environment?: string,
     * }  $config
     */
    public static function make(array $config): self
    {
        $environment = Environment::from($config['environment'] ?? 'sandbox');

        $auth = new GlsAuthenticator(
            clientId: $config['client_id'],
            clientSecret: $config['client_secret'],
            environment: $environment,
        );

        $connector = new GlsShopReturnsConnector($auth, $environment->baseUrl());

        return new self($connector, $config['app_id']);
    }

    public function lastResponse(): ?Response
    {
        return $this->lastResponse;
    }

    public function createReturnOrder(CreateReturnOrderData $data): ReturnOrderData
    {
        return ($this->lastResponse = $this->connector->send(new CreateReturnOrderRequest($this->appId, $data)))->throw()->dto();
    }

    public function createReturnOrderWithLabel(
        CreateReturnOrderData $data,
        ?LabelFormat $labelFormat = null,
        ?LabelDpi $labelDpi = null,
    ): ReturnOrderWithLabelData {
        return ($this->lastResponse = $this->connector->send(new CreateReturnOrderWithLabelRequest($this->appId, $data, $labelFormat, $labelDpi)))->throw()->dto();
    }

    public function createReturnOrderWithRawLabel(
        CreateReturnOrderData $data,
        LabelFormat $labelFormat,
        ?LabelDpi $labelDpi = null,
    ): string {
        $this->lastResponse = $this->connector->send(new CreateReturnOrderWithRawLabelRequest($this->appId, $data, $labelFormat, $labelDpi));
        $this->lastResponse->throw();

        return $this->lastResponse->body();
    }

    public function createReturnOrderWithQrCode(
        CreateReturnOrderData $data,
        ?LabelDpi $labelDpi = null,
    ): ReturnOrderWithQrCodeData {
        return ($this->lastResponse = $this->connector->send(new CreateReturnOrderWithQrCodeRequest($this->appId, $data, $labelDpi)))->throw()->dto();
    }

    public function createReturnOrderWithRawQrCode(
        CreateReturnOrderData $data,
        LabelFormat $labelFormat,
        ?LabelDpi $labelDpi = null,
    ): string {
        $this->lastResponse = $this->connector->send(new CreateReturnOrderWithRawQrCodeRequest($this->appId, $data, $labelFormat, $labelDpi));
        $this->lastResponse->throw();

        return $this->lastResponse->body();
    }

    public function getLabel(
        string $returnOrderId,
        LabelType $labelType = LabelType::Label,
        ?LabelFormat $labelFormat = null,
        ?LabelDpi $labelDpi = null,
    ): ReturnOrderWithLabelData|ReturnOrderWithQrCodeData {
        return ($this->lastResponse = $this->connector->send(new GetReturnOrderLabelRequest($returnOrderId, $labelType, $labelFormat, $labelDpi)))->throw()->dto();
    }

    public function getRawLabel(
        string $returnOrderId,
        LabelType $labelType,
        LabelFormat $labelFormat,
    ): string {
        $this->lastResponse = $this->connector->send(new GetReturnOrderRawLabelRequest($returnOrderId, $labelType, $labelFormat));
        $this->lastResponse->throw();

        return $this->lastResponse->body();
    }

    public function connector(): GlsShopReturnsConnector
    {
        return $this->connector;
    }
}
