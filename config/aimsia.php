<?php

return [
    'api_url' => env('APP_ENV') == 'local' ? env('LOCAL_API_URL') : env('PRODUCTION_API_URL'),
     
    'api_client_id' => env('API_CLIENT_ID'),
    'api_client_secret' => env('API_CLIENT_SECRET'),
    'api_username' => env('API_USERNAME'),
    'api_password' => env('API_PASSWORD'),
    
    'platform_id' => env('PLATFORM_ID'),
    'platform_product_published_id' => env('PLATFORM_PRODUCT_PUBLISHED_ID'),
    'platform_email_verification_url' => env('PLATFORM_EMAIL_VERIFICATION_URL'),
];