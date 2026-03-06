<?php

use SmartDato\GlsShopReturnsCustomer\Enums\Environment;

// config for SmartDato/GlsShopReturnsCustomer
return [

    'environment' => env('GLS_ENVIRONMENT', Environment::Sandbox->value),

    'app_id' => env('GLS_APP_ID', 'sandbox'),

    'client_id' => env('GLS_CLIENT_ID', ''),

    'client_secret' => env('GLS_CLIENT_SECRET', ''),

];
