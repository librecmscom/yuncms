<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Encryption Keys
    |--------------------------------------------------------------------------
    |
    | Passport uses encryption keys while generating secure access tokens for
    | your application. By default, the keys are stored as local files but
    | can be set via environment variables when that is more convenient.
    |
    */

    'private_key' => env('PASSPORT_PRIVATE_KEY'),

    'public_key' => env('PASSPORT_PUBLIC_KEY'),

    //设置令牌有效期1天
    'tokens_expire_in' => (int)env('PASSPORT_TOKENS_EXPIRE_IN', 1),

    //设置刷新令牌有效期7天
    'refresh_tokens_expire_in' => (int)env('PASSPORT_REFRESH_TOKENS_EXPIRE_IN', 7),

    //个人令牌有效期6天
    'personal_access_tokens_expire_in' => (int)env('PASSPORT_PERSONAL_ACCESS_TOKENS_EXPIRE_IN', 6),

    /*
    |--------------------------------------------------------------------------
    | Client UUIDs
    |--------------------------------------------------------------------------
    |
    | By default, Passport uses auto-incrementing primary keys when assigning
    | IDs to clients. However, if Passport is installed using the provided
    | --uuids switch, this will be set to "true" and UUIDs will be used.
    |
    */

    'client_uuids' => false,

    /*
    |--------------------------------------------------------------------------
    | Personal Access Client
    |--------------------------------------------------------------------------
    |
    | If you enable client hashing, you should set the personal access client
    | ID and unhashed secret within your environment file. The values will
    | get used while issuing fresh personal access tokens to your users.
    |
    */

    'personal_access_client' => [
        'id' => env('PASSPORT_PERSONAL_ACCESS_CLIENT_ID', 10000000),
        'secret' => env('PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET', 'teErmVYWFPk6yMXCVrXHRAfG9SRXE609MDYFu91E'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Passport Storage Driver
    |--------------------------------------------------------------------------
    |
    | This configuration value allows you to customize the storage options
    | for Passport, such as the database connection that should be used
    | by Passport's internal database models which store tokens, etc.
    |
    */

    'storage' => [
        'database' => [
            'connection' => env('DB_CONNECTION', 'mysql'),
        ],
    ],

    'cache' => [
        // Cache key prefix
        'prefix' => 'passport_',

        // The lifetime of passport cache(seconds).
        'expires_in' => 300,

        // Cache tags
        'tags' => [],
    ],

];
