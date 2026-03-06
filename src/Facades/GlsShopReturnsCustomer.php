<?php

namespace SmartDato\GlsShopReturnsCustomer\Facades;

use Illuminate\Support\Facades\Facade;
use SmartDato\GlsShopReturnsCustomer\Data\CreateReturnOrderData;
use SmartDato\GlsShopReturnsCustomer\Data\ReturnOrderData;
use SmartDato\GlsShopReturnsCustomer\Data\ReturnOrderWithLabelData;
use SmartDato\GlsShopReturnsCustomer\Data\ReturnOrderWithQrCodeData;
use SmartDato\GlsShopReturnsCustomer\Enums\LabelDpi;
use SmartDato\GlsShopReturnsCustomer\Enums\LabelFormat;
use SmartDato\GlsShopReturnsCustomer\Enums\LabelType;

/**
 * @method static ReturnOrderData createReturnOrder(CreateReturnOrderData $data)
 * @method static ReturnOrderWithLabelData createReturnOrderWithLabel(CreateReturnOrderData $data, ?LabelFormat $labelFormat = null, ?LabelDpi $labelDpi = null)
 * @method static string createReturnOrderWithRawLabel(CreateReturnOrderData $data, LabelFormat $labelFormat, ?LabelDpi $labelDpi = null)
 * @method static ReturnOrderWithQrCodeData createReturnOrderWithQrCode(CreateReturnOrderData $data, ?LabelDpi $labelDpi = null)
 * @method static string createReturnOrderWithRawQrCode(CreateReturnOrderData $data, LabelFormat $labelFormat, ?LabelDpi $labelDpi = null)
 * @method static ReturnOrderWithLabelData|ReturnOrderWithQrCodeData getLabel(string $returnOrderId, LabelType $labelType = LabelType::Label, ?LabelFormat $labelFormat = null, ?LabelDpi $labelDpi = null)
 * @method static string getRawLabel(string $returnOrderId, LabelType $labelType, LabelFormat $labelFormat)
 *
 * @see \SmartDato\GlsShopReturnsCustomer\GlsShopReturnsCustomer
 */
class GlsShopReturnsCustomer extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \SmartDato\GlsShopReturnsCustomer\GlsShopReturnsCustomer::class;
    }
}
