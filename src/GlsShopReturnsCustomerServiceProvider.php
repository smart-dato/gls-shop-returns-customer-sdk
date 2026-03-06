<?php

namespace SmartDato\GlsShopReturnsCustomer;

use SmartDato\GlsShopReturnsCustomer\Auth\GlsAuthenticator;
use SmartDato\GlsShopReturnsCustomer\Connectors\GlsShopReturnsConnector;
use SmartDato\GlsShopReturnsCustomer\Enums\Environment;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class GlsShopReturnsCustomerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('gls-shop-returns-customer-sdk')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(GlsAuthenticator::class, function () {
            return new GlsAuthenticator(
                clientId: (string) config('gls-shop-returns-customer-sdk.client_id'),
                clientSecret: (string) config('gls-shop-returns-customer-sdk.client_secret'),
                environment: Environment::from((string) config('gls-shop-returns-customer-sdk.environment')),
            );
        });

        $this->app->singleton(GlsShopReturnsConnector::class, function ($app) {
            $environment = Environment::from((string) config('gls-shop-returns-customer-sdk.environment'));

            return new GlsShopReturnsConnector(
                $app->make(GlsAuthenticator::class),
                $environment->baseUrl(),
            );
        });

        $this->app->singleton(GlsShopReturnsCustomer::class, function ($app) {
            return new GlsShopReturnsCustomer(
                connector: $app->make(GlsShopReturnsConnector::class),
                appId: (string) config('gls-shop-returns-customer-sdk.app_id'),
            );
        });
    }
}
