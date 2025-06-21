<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure your Midtrans settings for payment processing.
    | You can get these keys from your Midtrans dashboard.
    |
    */

    'server_key' => env('MIDTRANS_SERVER_KEY', ''),
    'client_key' => env('MIDTRANS_CLIENT_KEY', ''),
    'merchant_id' => env('MIDTRANS_MERCHANT_ID', ''),
    
    // Environment: 'sandbox' or 'production'
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    
    // Enable 3D Secure
    'is_3ds' => env('MIDTRANS_IS_3DS', true),
    
    // Snap settings
    'snap_url' => env('MIDTRANS_IS_PRODUCTION', false) 
        ? 'https://app.midtrans.com/snap/v1/transactions' 
        : 'https://app.sandbox.midtrans.com/snap/v1/transactions',
    
    // API URLs
    'api_url' => env('MIDTRANS_IS_PRODUCTION', false)
        ? 'https://api.midtrans.com'
        : 'https://api.sandbox.midtrans.com',
]; 