<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Debug
    |--------------------------------------------------------------------------
    | By enabling this option, all restrictions in a route will be bypassed.
    | Make sure to disable this during production.
    */

    'debug' => false,

    /*
    |--------------------------------------------------------------------------
    | Administrative Access
    |--------------------------------------------------------------------------
    | Declare role names that has all URI permissions.
    */

    'administrative_access' => ['Super Admin', 'Administrator'],

    /*
    |--------------------------------------------------------------------------
    | Hidden Roles
    |--------------------------------------------------------------------------
    |
    */

    'hidden_roles' => ['Super Admin'],

    /*
    |--------------------------------------------------------------------------
    | Routes Exception
    |--------------------------------------------------------------------------
    | Declare URI pattern inside to exclude
    | the database.
    */

    'routes_exception' => [
        'uri' => [

        ],

        'controllers' => [
            'Illuminate\Routing\ViewController',
            'App\Http\Controllers\Auth',
            'Laravel\Sanctum\Http\\Controllers\CsrfCookieController',

        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Guest URI Exception
    |--------------------------------------------------------------------------
    | Guest URI will still be recorded into database, but bypasses all
    | restriction access. URI declared in guest exception will not be
    | remove from their respective assigned roles. It is best to use this when
    | doing debugging, temporary routes or in testing mode.
    |
    | Note: Use asterisk (*) for wildcard declaration.
    |       Add @method to specify which route to be exempted.
    |
    | Example:
    |   - test/*
    |   - test@get
    */

    'guest_uri' => [

    ],

    /*
    |--------------------------------------------------------------------------
    | Table Name
    |--------------------------------------------------------------------------
    | Alternatively, you can customize the table name to be used in this
    | package.
    */

    'table_name' => env('SENTRY_TABLE', 'sentries'),

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    | By default all URI permissions are cached for 24 hours to speed up
    | performance. Any update event will trigger the cache is flushed
    | automatically.
    */

    'cache' => [

        'expiration_time' => DateInterval::createFromDateString('24 hours'),

        'key' => 'masterei.sentry.cache'
    ],

    /*
    |--------------------------------------------------------------------------
    | Package Resources Route Prefix
    |--------------------------------------------------------------------------
    |
    */

    'route_prefix' => 'sentry',
];
