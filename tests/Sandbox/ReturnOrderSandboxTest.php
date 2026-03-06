<?php

/**
 * Sandbox integration tests against the GLS Shop Returns sandbox environment.
 *
 * These tests make REAL API calls and require:
 * - A .env.testing file with sandbox credentials
 * - Valid GLS developer portal app with Shop Returns Sandbox API v3 enabled
 *
 * Run manually with: vendor/bin/pest --group=sandbox
 */

use Dotenv\Dotenv;
use SmartDato\GlsShopReturnsCustomer\Connectors\GlsShopReturnsConnector;
use SmartDato\GlsShopReturnsCustomer\Data\CreateReturnOrderData;
use SmartDato\GlsShopReturnsCustomer\Data\ReturnOrderData;
use SmartDato\GlsShopReturnsCustomer\Data\ReturnOrderWithLabelData;
use SmartDato\GlsShopReturnsCustomer\Data\ReturnOrderWithQrCodeData;
use SmartDato\GlsShopReturnsCustomer\Enums\Environment;
use SmartDato\GlsShopReturnsCustomer\Enums\LabelFormat;
use SmartDato\GlsShopReturnsCustomer\Enums\LabelType;
use SmartDato\GlsShopReturnsCustomer\GlsShopReturnsCustomer;

function sandboxAvailable(): bool
{
    return file_exists(dirname(__DIR__, 2).'/.env.testing');
}

function loadSandboxEnv(): void
{
    static $loaded = false;
    if (! $loaded) {
        Dotenv::createMutable(dirname(__DIR__, 2), '.env.testing')->load();
        $loaded = true;
    }
}

function sandboxConnector(): GlsShopReturnsConnector
{
    loadSandboxEnv();

    return new GlsShopReturnsConnector(
        environment: Environment::Sandbox,
        clientId: env('GLS_CLIENT_ID'),
        clientSecret: env('GLS_CLIENT_SECRET'),
    );
}

function sandboxSdk(): GlsShopReturnsCustomer
{
    $connector = sandboxConnector();
    $connector->refreshAuthenticator();

    return new GlsShopReturnsCustomer(
        connector: $connector,
        appId: env('GLS_APP_ID', 'sandbox'),
    );
}

function sandboxReturnOrderData(): CreateReturnOrderData
{
    return CreateReturnOrderData::from([
        'originalOrderReference' => 'TEST-ORDER-'.time(),
        'returnReason' => 'Sandbox integration test',
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
}

it('can obtain an OAuth token from the sandbox', function () {
    loadSandboxEnv();

    $connector = sandboxConnector();
    $authenticator = $connector->getAccessToken();

    dump('Token type:', get_class($authenticator));
    dump('Token expired:', $authenticator->hasExpired() ? 'yes' : 'no');

    expect($authenticator)->toBeInstanceOf(\Saloon\Contracts\OAuthAuthenticator::class)
        ->and($authenticator->getAccessToken())->not->toBeEmpty();
})->group('sandbox')
    ->skip(fn () => ! sandboxAvailable(), 'Skipped: .env.testing not found');

it('can create a return order in the sandbox', function () {
    $sdk = sandboxSdk();

    $response = $sdk->createReturnOrder(sandboxReturnOrderData());

    dump('Return Order ID:', $response->returnOrderId);
    dump('Track ID:', $response->references?->trackId ?? 'N/A');
    dump('Parcel ID:', $response->references?->parcelId ?? 'N/A');

    expect($response)->toBeInstanceOf(ReturnOrderData::class)
        ->and($response->returnOrderId)->not->toBeEmpty();
})->group('sandbox')
    ->skip(fn () => ! sandboxAvailable(), 'Skipped: .env.testing not found');

it('can create a return order with label (JSON) in the sandbox', function () {
    $sdk = sandboxSdk();

    $response = $sdk->createReturnOrderWithLabel(
        sandboxReturnOrderData(),
        LabelFormat::Pdf,
    );

    dump('Return Order ID:', $response->returnOrderId);
    dump('Label content type:', $response->label->contentType);
    dump('Label content length:', strlen($response->label->content));

    expect($response)->toBeInstanceOf(ReturnOrderWithLabelData::class)
        ->and($response->returnOrderId)->not->toBeEmpty()
        ->and($response->label->contentType)->toBe('application/pdf')
        ->and($response->label->content)->not->toBeEmpty();
})->group('sandbox')
    ->skip(fn () => ! sandboxAvailable(), 'Skipped: .env.testing not found');

it('can create a return order with raw label in the sandbox', function () {
    $sdk = sandboxSdk();

    $rawPdf = $sdk->createReturnOrderWithRawLabel(
        sandboxReturnOrderData(),
        LabelFormat::Pdf,
    );

    dump('Raw PDF size (bytes):', strlen($rawPdf));

    expect($rawPdf)->not->toBeEmpty()
        ->and(str_starts_with($rawPdf, '%PDF'))->toBeTrue();
})->group('sandbox')
    ->skip(fn () => ! sandboxAvailable(), 'Skipped: .env.testing not found');

it('can retrieve a label for an existing return order in the sandbox', function () {
    $sdk = sandboxSdk();

    // First create an order
    $created = $sdk->createReturnOrder(sandboxReturnOrderData());

    dump('Created Return Order ID:', $created->returnOrderId);

    // Then retrieve its label
    $response = $sdk->getLabel(
        $created->returnOrderId,
        LabelType::Label,
        LabelFormat::Pdf,
    );

    dump('Retrieved label content type:', $response->label->contentType);
    dump('Retrieved label content length:', strlen($response->label->content));

    expect($response)->toBeInstanceOf(ReturnOrderWithLabelData::class)
        ->and($response->label->content)->not->toBeEmpty();
})->group('sandbox')
    ->skip(fn () => ! sandboxAvailable(), 'Skipped: .env.testing not found');

it('can create a return order with QR code in the sandbox', function () {
    $sdk = sandboxSdk();

    $response = $sdk->createReturnOrderWithQrCode(sandboxReturnOrderData());

    dump('Return Order ID:', $response->returnOrderId);
    dump('QR code content type:', $response->parcelShopQrCode->contentType);
    dump('QR code content length:', strlen($response->parcelShopQrCode->content));

    expect($response)->toBeInstanceOf(ReturnOrderWithQrCodeData::class)
        ->and($response->returnOrderId)->not->toBeEmpty()
        ->and($response->parcelShopQrCode->content)->not->toBeEmpty();
})->group('sandbox')
    ->skip(fn () => ! sandboxAvailable(), 'Skipped: .env.testing not found');
