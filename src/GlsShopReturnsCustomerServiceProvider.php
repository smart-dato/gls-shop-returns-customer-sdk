<?php

namespace SmartDato\GlsShopReturnsCustomer;

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
        $this->app->singleton(GlsShopReturnsConnector::class, function () {
            return new GlsShopReturnsConnector(
                environment: Environment::from(config('gls-shop-returns-customer-sdk.environment')),
                clientId: config('gls-shop-returns-customer-sdk.client_id'),
                clientSecret: config('gls-shop-returns-customer-sdk.client_secret'),
            );
        });

        $this->app->singleton(GlsShopReturnsCustomer::class, function ($app) {
            return new GlsShopReturnsCustomer(
                connector: $app->make(GlsShopReturnsConnector::class),
                appId: config('gls-shop-returns-customer-sdk.app_id'),
            );
        });
    }
}
