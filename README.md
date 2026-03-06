# GLS Shop Returns Customer SDK

[![Latest Version on Packagist](https://img.shields.io/packagist/v/smart-dato/gls-shop-returns-customer-sdk.svg?style=flat-square)](https://packagist.org/packages/smart-dato/gls-shop-returns-customer-sdk)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/smart-dato/gls-shop-returns-customer-sdk/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/smart-dato/gls-shop-returns-customer-sdk/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/smart-dato/gls-shop-returns-customer-sdk/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/smart-dato/gls-shop-returns-customer-sdk/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/smart-dato/gls-shop-returns-customer-sdk.svg?style=flat-square)](https://packagist.org/packages/smart-dato/gls-shop-returns-customer-sdk)

Laravel package for integrating with the GLS Shop Returns Customer API v3. Supports generating return labels and parcel shop QR codes across European countries. Built on [Saloon 3.x](https://docs.saloon.dev) for HTTP and [Spatie Laravel Data 4.x](https://spatie.be/docs/laravel-data) for DTOs.

## Requirements

- PHP 8.4+
- Laravel 11 or 12

## Supported Countries

Return labels can be generated from: AT, BE, BG, CH, CZ, DE, DK, EE, ES, FI, FR, GB, GR, HR, HU, IE, IT, LT, LU, LV, NL, PL, PT, RO, SE, SI, SK.

## Installation

Install the package via Composer:

```bash
composer require smart-dato/gls-shop-returns-customer-sdk
```

Publish the config file:

```bash
php artisan vendor:publish --tag="gls-shop-returns-customer-sdk-config"
```

## Configuration

Add the following environment variables to your `.env` file:

```env
GLS_CLIENT_ID=your-client-id
GLS_CLIENT_SECRET=your-client-secret
GLS_APP_ID=your-app-id
GLS_ENVIRONMENT=sandbox
```

The published config file (`config/gls-shop-returns-customer-sdk.php`) contains all available options:

```php
return [
    'environment' => env('GLS_ENVIRONMENT', 'sandbox'),  // sandbox, qas, production
    'app_id'      => env('GLS_APP_ID', 'sandbox'),
    'client_id'   => env('GLS_CLIENT_ID', ''),
    'client_secret' => env('GLS_CLIENT_SECRET', ''),
];
```

Available environments:

| Environment | Base URL |
|-------------|----------|
| `sandbox` | `https://api-sandbox.gls-group.net/order-management/shop-returns/v3` |
| `qas` | `https://api-qas.gls-group.net/order-management/shop-returns/v3` |
| `production` | `https://api.gls-group.net/order-management/shop-returns/v3` |

> **Note:** In the sandbox environment, use `sandbox` as the `GLS_APP_ID`. Your real app ID is only for production.

## Usage

Resolve the SDK from the container (or use the `GlsShopReturnsCustomer` facade):

```php
use SmartDato\GlsShopReturnsCustomer\GlsShopReturnsCustomer;

$gls = app(GlsShopReturnsCustomer::class);
```

### Create a Return Order

```php
use SmartDato\GlsShopReturnsCustomer\Data\CreateReturnOrderData;

$request = CreateReturnOrderData::from([
    'originalOrderReference' => 'ORDER-12345',
    'returnReason' => 'Product not as described',
    'sender' => [
        'personName' => 'Max Mustermann',
        'address' => [
            'street' => 'Musterstr. 1',
            'city' => 'Berlin',
            'zipCode' => '10115',
            'countryCode' => 'DE',
        ],
    ],
]);

$response = $gls->createReturnOrder($request);

$response->returnOrderId;          // "d137d614-ccc0-4d09-b956-9fc7483e7993"
$response->references->trackId;    // "YUNKGBSU"
$response->references->parcelId;   // "100002180461"
```

### Create a Return Order with Label (Base64)

```php
use SmartDato\GlsShopReturnsCustomer\Enums\LabelFormat;

$response = $gls->createReturnOrderWithLabel($request, LabelFormat::Pdf);

$response->returnOrderId;
$response->label->contentType;  // "application/pdf"
$response->label->content;      // Base64-encoded PDF
```

### Create a Return Order with Raw Label

```php
$rawPdf = $gls->createReturnOrderWithRawLabel($request, LabelFormat::Pdf);

// $rawPdf is the raw PDF binary content
file_put_contents('return-label.pdf', $rawPdf);
```

### Create a Return Order with QR Code

For parcel shop drop-off (supported in AT, BE, DE, DK, LU):

```php
$response = $gls->createReturnOrderWithQrCode($request);

$response->parcelShopQrCode->contentType;  // "application/pdf"
$response->parcelShopQrCode->content;      // Base64-encoded QR code
```

### Retrieve a Label for an Existing Order

```php
use SmartDato\GlsShopReturnsCustomer\Enums\LabelType;
use SmartDato\GlsShopReturnsCustomer\Enums\LabelFormat;

// Get label as JSON (base64)
$response = $gls->getLabel(
    returnOrderId: 'd137d614-ccc0-4d09-b956-9fc7483e7993',
    labelType: LabelType::Label,
    labelFormat: LabelFormat::Pdf,
);

// Get raw label binary
$rawLabel = $gls->getRawLabel(
    returnOrderId: 'd137d614-ccc0-4d09-b956-9fc7483e7993',
    labelType: LabelType::Label,
    labelFormat: LabelFormat::Pdf,
);
```

### Optional Fields

```php
$request = CreateReturnOrderData::from([
    'originalOrderReference' => 'ORDER-12345',
    'returnReason' => 'Wrong size',
    'sender' => [
        'personName' => 'Maria Garcia',
        'companyName' => 'Garcia SL',
        'email' => 'maria@example.com',  // Required for ES, FR, IT, SE
        'address' => [
            'street' => 'Calle Mayor 1',
            'city' => 'Madrid',
            'zipCode' => '28001',
            'countryCode' => 'ES',
        ],
    ],
    'parcel' => [
        'weight' => 2.5,  // kg, defaults to 1.5
    ],
    'options' => [
        'languageCode' => 'es',
        'confirmationMail' => [
            'sendTo' => ['customer@example.com'],
            'attachments' => [
                'label' => ['include' => true],
                'parcelShopQrCode' => ['include' => true],
            ],
        ],
        'label' => [
            'layout' => 'A6',  // A4 (default) or A6
        ],
    ],
    'additionalReferences' => [
        'broker' => '#BROKER123#',
        'nationalContactId' => '111aaagZ0A',  // FR only
    ],
]);
```

### Using the Facade

```php
use SmartDato\GlsShopReturnsCustomer\Facades\GlsShopReturnsCustomer;

$response = GlsShopReturnsCustomer::createReturnOrder($request);
$label = GlsShopReturnsCustomer::createReturnOrderWithLabel($request, LabelFormat::Pdf);
$rawPdf = GlsShopReturnsCustomer::createReturnOrderWithRawLabel($request, LabelFormat::Pdf);
```

## Available Enums

```php
use SmartDato\GlsShopReturnsCustomer\Enums\Environment;    // Sandbox, Qas, Production
use SmartDato\GlsShopReturnsCustomer\Enums\LabelFormat;     // Pdf, Zpl, Png
use SmartDato\GlsShopReturnsCustomer\Enums\LabelType;       // Label, ParcelShopQrCode
use SmartDato\GlsShopReturnsCustomer\Enums\LabelDpi;        // Dpi152, Dpi203, Dpi300
use SmartDato\GlsShopReturnsCustomer\Enums\LabelLayout;     // A4, A6
use SmartDato\GlsShopReturnsCustomer\Enums\LanguageCode;    // German, English, French, Spanish, ...
```

## Error Handling

API errors are thrown as `GlsApiException`:

```php
use SmartDato\GlsShopReturnsCustomer\Exceptions\GlsApiException;

try {
    $response = $gls->createReturnOrder($request);
} catch (GlsApiException $e) {
    $e->getMessage();              // Error message from the API
    $e->getCode();                 // HTTP status code (400, 401, 403, 429, etc.)
    $e->errors;                    // Array of ErrorData objects
    $e->errors[0]->type;           // "400-UNKNOWN-KEY"
    $e->errors[0]->fieldName;      // "layout"
    $e->errors[0]->fieldValue;     // "XYZ"
    $e->errors[0]->message;        // "must be one of [A4, A6]"
}
```

## Testing

```bash
composer test             # Run tests
composer analyse          # Static analysis (PHPStan level 5)
composer format           # Code style (Laravel Pint)
composer test-coverage    # Tests with coverage report
```

### Sandbox Integration Tests

To run tests against the real GLS sandbox API:

1. Copy `.env.testing.example` to `.env.testing` and fill in your credentials
2. Run: `vendor/bin/pest --group=sandbox`

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [SmartDato](https://github.com/smart-dato)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
