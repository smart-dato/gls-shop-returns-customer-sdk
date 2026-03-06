<?php

namespace SmartDato\GlsShopReturnsCustomer;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class GlsShopReturnsCustomerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('gls-shop-returns-customer-sdk')
            ->hasConfigFile();
    }
}
