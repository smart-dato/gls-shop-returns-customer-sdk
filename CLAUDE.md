# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Laravel package (SDK) for the GLS Shop Returns Customer API v3. Wraps the GLS REST API for generating return labels across European countries. Built with Spatie's `laravel-package-tools`.

- **Namespace:** `SmartDato\GlsShopReturnsCustomer`
- **PHP:** ^8.4
- **Laravel:** 11.x / 12.x
- **Package name:** `smart-dato/gls-shop-returns-customer-sdk`

## Commands

```bash
composer test              # Run tests (Pest 4)
vendor/bin/pest --filter="test name"  # Run a single test
composer analyse           # Static analysis (PHPStan level 5, via Larastan)
composer format            # Code style fix (Laravel Pint)
```

## Architecture

- `src/GlsShopReturnsCustomerServiceProvider.php` - Package service provider (Spatie PackageServiceProvider), registers config
- `src/GlsShopReturnsCustomer.php` - Main SDK class (currently a scaffold)
- `src/Facades/GlsShopReturnsCustomer.php` - Laravel facade
- `config/gls-shop-returns-customer-sdk.php` - Published config file
- `docs/isrs-standard-v3.yaml` - OpenAPI 3.0 spec for the GLS Shop Returns Customer API v3 (the source of truth for API endpoints/models)

## Testing

Uses **Pest 4** with `orchestra/testbench` for Laravel package testing. All tests extend `Tests\TestCase` (configured in `tests/Pest.php`). Architecture tests in `tests/ArchTest.php` enforce no `dd`/`dump`/`ray` usage.

## CI

GitHub Actions runs tests across PHP 8.3/8.4, Laravel 11/12, on ubuntu and windows, with both `prefer-lowest` and `prefer-stable` dependency strategies.
