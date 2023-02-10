<?php

namespace Masterei\Sentry\General;

use Masterei\Sentry\Sentry;

class Guard
{
    public function __construct()
    {
        Cache::ensurePackageCache();
    }

    public function accessEvaluator($route, $method)
    {
        // allow all access on debugging mode
        if(Config::get('debug')){
            return true;
        }

        $uri = $this->prepareURI($route, $method);
        if(Sentry::isGuestURI($uri) // allow if uri is in guest exception list
            || !Sentry::verifyURIDatabaseExist($uri) // allow if uri does not exist in database
            || (auth()->check() && auth()->user()->hasAccess($uri))){ // check authenticated user if it has uri access
            return true;
        }

        return false;
    }

    protected function prepareURI($route, $method)
    {
        // get current route
        if(empty($route)){
            return URI::getCurrentURI();
        }

        // plain uri
        if(str_contains($route, '/')){
            return URI::formatURI(trim(explode('@', $route)[0], '/'), $method);
        }

        return URI::getURIFromRouteName($route);
    }
}
