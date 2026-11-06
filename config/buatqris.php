<?php

return [
    /*
    |--------------------------------------------------------------------------
    | BuatQris Open API Configuration
    |--------------------------------------------------------------------------
    |
    | Documentation: https://api.buatqris.site
    | Base URL: https://api.buatqris.site
    |
    */

    'base_url' => env('BUATQRIS_BASE_URL', 'https://api.buatqris.site'),

    // Credentials from dashboard: Open API -> Kredensial API
    'account_id' => env('BUATQRIS_ACCOUNT_ID', ''),
    'secret_token' => env('BUATQRIS_SECRET_TOKEN', ''),

    // Webhook Signing Secret from dashboard: Open API -> Signing Secret
    'signing_secret' => env('BUATQRIS_SIGNING_SECRET', ''),

    // 'user' = admin fee deducted from merchant balance (default)
    // 'buyer' = admin fee added to buyer's total
    'fee_by' => env('BUATQRIS_FEE_BY', 'user'),

    // Specific QRIS method (null = default by admin, or qris_one, qris_two, qris_three, qris_four)
    'method' => env('BUATQRIS_METHOD', null),

    // Test / Sandbox mode (true will send test=1, false for real money transactions)
    'test_mode' => (bool) env('BUATQRIS_TEST_MODE', false),

    // Custom Webhook Callback URL (defaults to null, which uses BuatQris dashboard setting)
    'callback_url' => env('BUATQRIS_CALLBACK_URL', null),
];
