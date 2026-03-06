<?php

namespace SmartDato\GlsShopReturnsCustomer\Connectors;

use Saloon\Contracts\Authenticator;
use Saloon\Http\Connector;
use Saloon\Http\Response;
use SmartDato\GlsShopReturnsCustomer\Auth\GlsAuthenticator;
use SmartDato\GlsShopReturnsCustomer\Exceptions\GlsApiException;

class GlsShopReturnsConnector extends Connector
{
    public function __construct(
        protected GlsAuthenticator $glsAuthenticator,
        protected ?string $baseUrl = null,
    ) {}

    public function resolveBaseUrl(): string
    {
        return $this->baseUrl ?? '';
    }

    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
        ];
    }

    protected function defaultAuth(): ?Authenticator
    {
        return $this->glsAuthenticator;
    }

    public function getRequestException(Response $response, ?\Throwable $senderException): ?\Throwable
    {
        return GlsApiException::fromResponse($response);
    }
}
