<?php

namespace SmartDato\GlsShopReturnsCustomer\Auth;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Saloon\Contracts\Authenticator;
use Saloon\Http\PendingRequest;
use SmartDato\GlsShopReturnsCustomer\Enums\Environment;

class GlsAuthenticator implements Authenticator
{
    public function __construct(
        protected string $clientId,
        protected string $clientSecret,
        protected Environment $environment,
    ) {}

    public function set(PendingRequest $pendingRequest): void
    {
        $token = $this->getToken();

        $pendingRequest->headers()->add('Authorization', 'Bearer '.$token);
    }

    protected function getToken(): string
    {
        return Cache::remember('gls_shop_returns_oauth_token', $this->getTokenTtl(), function () {
            return $this->fetchToken();
        });
    }

    protected function fetchToken(): string
    {
        $response = Http::asForm()->post($this->environment->tokenUrl(), [
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ]);

        $response->throw();

        return $response->json('access_token');
    }

    protected function getTokenTtl(): int
    {
        return 3300; // 55 minutes (token typically valid for 60 min)
    }
}
