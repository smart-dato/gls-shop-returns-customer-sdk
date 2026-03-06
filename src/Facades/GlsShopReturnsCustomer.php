<?php

namespace SmartDato\GlsShopReturnsCustomer\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \SmartDato\GlsShopReturnsCustomer\GlsShopReturnsCustomer
 */
class GlsShopReturnsCustomer extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \SmartDato\GlsShopReturnsCustomer\GlsShopReturnsCustomer::class;
    }
}
