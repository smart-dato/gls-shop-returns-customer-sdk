<?php

namespace SmartDato\GlsShopReturnsCustomer\Enums;

enum Environment: string
{
    case Sandbox = 'sandbox';
    case Qas = 'qas';
    case Production = 'production';

    public function baseUrl(): string
    {
        return match ($this) {
            self::Sandbox => 'https://api-sandbox.gls-group.net/order-management/shop-returns/v3',
            self::Qas => 'https://api-qas.gls-group.net/order-management/shop-returns/v3',
            self::Production => 'https://api.gls-group.net/order-management/shop-returns/v3',
        };
    }

    public function tokenUrl(): string
    {
        return match ($this) {
            self::Sandbox => 'https://api-sandbox.gls-group.net/oauth2/v2/token',
            self::Qas => 'https://api-qas.gls-group.net/oauth2/v2/token',
            self::Production => 'https://api.gls-group.net/oauth2/v2/token',
        };
    }
}
