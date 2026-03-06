<?php

namespace SmartDato\GlsShopReturnsCustomer\Connectors;

use Saloon\Contracts\OAuthAuthenticator;
use Saloon\Helpers\OAuth2\OAuthConfig;
use Saloon\Http\Connector;
use Saloon\Http\Response;
use Saloon\Traits\OAuth2\ClientCredentialsGrant;
use SmartDato\GlsShopReturnsCustomer\Enums\Environment;
use SmartDato\GlsShopReturnsCustomer\Exceptions\GlsApiException;

class GlsShopReturnsConnector extends Connector
{
    use ClientCredentialsGrant;

    private ?OAuthAuthenticator $cachedAuthenticator = null;

    public function __construct(
        public readonly Environment $environment,
        public readonly string $clientId,
        public readonly string $clientSecret,
    ) {}

    public function resolveBaseUrl(): string
    {
        return $this->environment->baseUrl();
    }

    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
        ];
    }

    protected function defaultOauthConfig(): OAuthConfig
    {
        return OAuthConfig::make()
            ->setClientId($this->clientId)
            ->setClientSecret($this->clientSecret)
            ->setTokenEndpoint($this->environment->tokenUrl());
    }

    public function refreshAuthenticator(): void
    {
        // Skip if authenticator was set externally (e.g. in tests)
        $existingAuth = $this->getAuthenticator();
        if ($existingAuth !== null) {
            if ($existingAuth instanceof OAuthAuthenticator && $existingAuth->hasExpired()) {
                // Token expired, re-fetch
            } else {
                return;
            }
        }

        if ($this->cachedAuthenticator && ! $this->cachedAuthenticator->hasExpired()) {
            $this->authenticate($this->cachedAuthenticator);

            return;
        }

        $this->cachedAuthenticator = $this->getAccessToken();
        $this->authenticate($this->cachedAuthenticator);
    }

    public function getRequestException(Response $response, ?\Throwable $senderException): ?\Throwable
    {
        return GlsApiException::fromResponse($response);
    }
}
