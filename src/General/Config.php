<?php

namespace Masterei\Sentry\General;

class Config
{
    protected static $config_name = 'sentry';

    const GUARD = 'web';

    const FILLABLE = [
        'group',
        'route_name',
        'description'
    ];

    const HIDDEN = [
        'pkg_group',
        'pkg_route_name'
    ];

    const ALLOWABLE_METHODS = [
        'GET',
        'POST',
        'PATCH',
        'PUT',
        'DELETE'
    ];

    public static function get($key)
    {
        return config(self::$config_name . ".$key");
    }

    public static function assetUrl($path)
    {
        return url('assets/' . Config::get('route_prefix') . '/' . $path);
    }
}
