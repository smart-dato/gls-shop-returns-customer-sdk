<?php

use SmartDato\GlsShopReturnsCustomer\Connectors\GlsShopReturnsConnector;
use SmartDato\GlsShopReturnsCustomer\Data\CreateReturnOrderData;
use SmartDato\GlsShopReturnsCustomer\Data\ReturnOrderData;
use SmartDato\GlsShopReturnsCustomer\Data\ReturnOrderWithLabelData;
use SmartDato\GlsShopReturnsCustomer\Data\ReturnOrderWithQrCodeData;
use SmartDato\GlsShopReturnsCustomer\Enums\Environment;
use SmartDato\GlsShopReturnsCustomer\Enums\LabelDpi;
use SmartDato\GlsShopReturnsCustomer\Enums\LabelFormat;
use SmartDato\GlsShopReturnsCustomer\Enums\LabelType;
use SmartDato\GlsShopReturnsCustomer\Exceptions\GlsApiException;
use SmartDato\GlsShopReturnsCustomer\GlsShopReturnsCustomer;
use SmartDato\GlsShopReturnsCustomer\Requests\CreateReturnOrderRequest;
use SmartDato\GlsShopReturnsCustomer\Requests\CreateReturnOrderWithLabelRequest;
use SmartDato\GlsShopReturnsCustomer\Requests\CreateReturnOrderWithQrCodeRequest;
use SmartDato\GlsShopReturnsCustomer\Requests\CreateReturnOrderWithRawLabelRequest;
use SmartDato\GlsShopReturnsCustomer\Requests\GetReturnOrderLabelRequest;
use SmartDato\GlsShopReturnsCustomer\Requests\GetReturnOrderRawLabelRequest;

function createTestOrderData(): CreateReturnOrderData
{
    return CreateReturnOrderData::from([
        'originalOrderReference' => 'ORDER-123',
        'returnReason' => 'Not needed',
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

function createConnector(): GlsShopReturnsConnector
{
    return new GlsShopReturnsConnector(
        environment: Environment::Sandbox,
        clientId: 'test-client-id',
        clientSecret: 'test-client-secret',
    );
}

// --- Enum tests ---

it('resolves environment base urls', function () {
    expect(Environment::Sandbox->baseUrl())->toContain('api-sandbox');
    expect(Environment::Qas->baseUrl())->toContain('api-qas');
    expect(Environment::Production->baseUrl())->toBe('https://api.gls-group.net/order-management/shop-returns/v3');
});

it('resolves environment token urls', function () {
    expect(Environment::Sandbox->tokenUrl())->toContain('api-sandbox');
    expect(Environment::Production->tokenUrl())->toBe('https://api.gls-group.net/oauth2/v2/token');
});

// --- Data tests ---

it('creates return order data from array', function () {
    $data = createTestOrderData();

    expect($data->originalOrderReference)->toBe('ORDER-123')
        ->and($data->returnReason)->toBe('Not needed')
        ->and($data->sender->personName)->toBe('Max Mustermann')
        ->and($data->sender->address->countryCode)->toBe('DE')
        ->and($data->parcel)->toBeNull()
        ->and($data->options)->toBeNull()
        ->and($data->additionalReferences)->toBeNull();
});

it('serializes return order data to array', function () {
    $data = createTestOrderData();
    $array = $data->toArray();

    expect($array)->toHaveKey('originalOrderReference', 'ORDER-123')
        ->and($array['sender']['personName'])->toBe('Max Mustermann')
        ->and($array['sender']['address']['countryCode'])->toBe('DE');
});

it('creates return order response from json', function () {
    $data = ReturnOrderData::from([
        'returnOrderId' => 'abc-123',
        'references' => ['trackId' => 'TRACK1', 'parcelId' => '123456789012'],
    ]);

    expect($data->returnOrderId)->toBe('abc-123')
        ->and($data->references->trackId)->toBe('TRACK1')
        ->and($data->references->parcelId)->toBe('123456789012');
});

it('creates return order with label response', function () {
    $data = ReturnOrderWithLabelData::from([
        'returnOrderId' => 'abc-123',
        'label' => ['contentType' => 'application/pdf', 'content' => 'base64content'],
        'references' => ['trackId' => 'TRACK1'],
    ]);

    expect($data->returnOrderId)->toBe('abc-123')
        ->and($data->label->contentType)->toBe('application/pdf')
        ->and($data->label->content)->toBe('base64content');
});

it('creates return order with qr code response', function () {
    $data = ReturnOrderWithQrCodeData::from([
        'returnOrderId' => 'abc-123',
        'parcelShopQrCode' => ['contentType' => 'image/png', 'content' => 'qrbase64'],
    ]);

    expect($data->returnOrderId)->toBe('abc-123')
        ->and($data->parcelShopQrCode->contentType)->toBe('image/png')
        ->and($data->parcelShopQrCode->content)->toBe('qrbase64');
});

// --- Request endpoint tests ---

it('builds create return order endpoint', function () {
    $request = new CreateReturnOrderRequest('sandbox', createTestOrderData());

    expect($request->resolveEndpoint())->toBe('/sandbox/return-orders');
});

it('builds create return order with label endpoint', function () {
    $request = new CreateReturnOrderWithLabelRequest(
        'sandbox',
        createTestOrderData(),
        LabelFormat::Pdf,
        LabelDpi::Dpi300,
    );

    expect($request->resolveEndpoint())->toBe('/sandbox/return-orders/label');
});

it('builds create return order with raw label endpoint', function () {
    $request = new CreateReturnOrderWithRawLabelRequest(
        'sandbox',
        createTestOrderData(),
        LabelFormat::Png,
    );

    expect($request->resolveEndpoint())->toBe('/sandbox/return-orders/label/png');
});

it('builds create return order with qr code endpoint', function () {
    $request = new CreateReturnOrderWithQrCodeRequest(
        'sandbox',
        createTestOrderData(),
    );

    expect($request->resolveEndpoint())->toBe('/sandbox/return-orders/parcelshop-qrcode');
});

it('builds get return order label endpoint', function () {
    $request = new GetReturnOrderLabelRequest('abc-123', LabelType::Label);

    expect($request->resolveEndpoint())->toBe('/return-orders/abc-123/label');
});

it('builds get return order raw label endpoint', function () {
    $request = new GetReturnOrderRawLabelRequest('abc-123', LabelType::ParcelShopQrCode, LabelFormat::Pdf);

    expect($request->resolveEndpoint())->toBe('/return-orders/abc-123/parcelshop-qrcode/pdf');
});

// --- Connector tests ---

it('resolves connector base url from environment', function () {
    $connector = createConnector();

    expect($connector->resolveBaseUrl())->toBe('https://api-sandbox.gls-group.net/order-management/shop-returns/v3');
});

it('configures oauth config', function () {
    $connector = createConnector();
    $config = $connector->oauthConfig();

    expect($config->getClientId())->toBe('test-client-id')
        ->and($config->getClientSecret())->toBe('test-client-secret')
        ->and($config->getTokenEndpoint())->toContain('api-sandbox');
});

// --- SDK integration with MockClient ---

it('creates return order via sdk with mock', function () {
    $connector = createConnector();

    $mockClient = new Saloon\Http\Faking\MockClient([
        CreateReturnOrderRequest::class => Saloon\Http\Faking\MockResponse::make([
            'returnOrderId' => 'order-456',
            'references' => ['trackId' => 'TRACK-789', 'parcelId' => '111222333444'],
        ], 201),
    ]);

    $connector->withMockClient($mockClient);
    $connector->authenticate(new Saloon\Http\Auth\AccessTokenAuthenticator('fake-token'));

    $sdk = new GlsShopReturnsCustomer($connector, 'sandbox');
    $result = $sdk->createReturnOrder(createTestOrderData());

    expect($result)->toBeInstanceOf(ReturnOrderData::class)
        ->and($result->returnOrderId)->toBe('order-456')
        ->and($result->references->trackId)->toBe('TRACK-789');
});

it('creates return order with label via sdk with mock', function () {
    $connector = createConnector();

    $mockClient = new Saloon\Http\Faking\MockClient([
        CreateReturnOrderWithLabelRequest::class => Saloon\Http\Faking\MockResponse::make([
            'returnOrderId' => 'order-789',
            'label' => ['contentType' => 'application/pdf', 'content' => 'PDFCONTENT'],
            'references' => ['trackId' => 'TRACK-001'],
        ], 201),
    ]);

    $connector->withMockClient($mockClient);
    $connector->authenticate(new Saloon\Http\Auth\AccessTokenAuthenticator('fake-token'));

    $sdk = new GlsShopReturnsCustomer($connector, 'sandbox');
    $result = $sdk->createReturnOrderWithLabel(createTestOrderData(), LabelFormat::Pdf);

    expect($result)->toBeInstanceOf(ReturnOrderWithLabelData::class)
        ->and($result->label->contentType)->toBe('application/pdf')
        ->and($result->label->content)->toBe('PDFCONTENT');
});

it('throws gls api exception on error response', function () {
    $connector = createConnector();

    $mockClient = new Saloon\Http\Faking\MockClient([
        CreateReturnOrderRequest::class => Saloon\Http\Faking\MockResponse::make([
            'errors' => [
                [
                    'type' => '400-UNKNOWN-KEY',
                    'fieldName' => 'layout',
                    'fieldValue' => 'XYZ',
                    'message' => 'must be one of [A4, A6]',
                ],
            ],
        ], 400),
    ]);

    $connector->withMockClient($mockClient);
    $connector->authenticate(new Saloon\Http\Auth\AccessTokenAuthenticator('fake-token'));

    $sdk = new GlsShopReturnsCustomer($connector, 'sandbox');
    $sdk->createReturnOrder(createTestOrderData());
})->throws(GlsApiException::class, 'must be one of [A4, A6]');

// --- Service provider tests ---

it('resolves sdk from container', function () {
    config()->set('gls-shop-returns-customer-sdk.environment', 'sandbox');
    config()->set('gls-shop-returns-customer-sdk.app_id', 'sandbox');
    config()->set('gls-shop-returns-customer-sdk.client_id', 'test-id');
    config()->set('gls-shop-returns-customer-sdk.client_secret', 'test-secret');

    $sdk = app(GlsShopReturnsCustomer::class);

    expect($sdk)->toBeInstanceOf(GlsShopReturnsCustomer::class);
});
