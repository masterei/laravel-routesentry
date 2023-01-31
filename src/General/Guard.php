<?php

namespace Masterei\Sentry\General;

class Guard
{
    public function __construct()
    {
        Cache::ensurePackageCache();
    }

    public static function init()
    {
        return new self();
    }

    public function hasAccess($route = null, $method = 'get')
    {
        return $this->accessEvaluator($route, $method, 'hasPermissionTo');
    }

    public function checkAccess($route = null, $method = 'get')
    {
        return $this->accessEvaluator($route, $method, 'checkPermissionTo');
    }

    protected function accessEvaluator($route, $method, $execute_method)
    {
        // allow all access
        if(Config::get('debug')){
            return true;
        }

        $uri = $this->prepareURI($route, $method);

        // allow access if uri does not exist in database
        if(!URI::verifyURIDatabaseExist($uri)){
            return true;
        }

        // allow access if uri is in guest exception
        if(Assessor::isGuestURI($uri)){
            return true;
        }

        // check authenticated user with uri access
        if(auth()->check() && auth()->user()->$execute_method($uri)){
            return true;
        }

        return false;
    }

    protected function prepareURI($route, $method)
    {
        // base on current route
        if(empty($route)){
            return URI::getCurrentURI();
        }

        // plain uri given
        if(str_contains($route, '/')){
            return URI::formatURI(trim($route, '/'), $method);
        }

        // base on route name
        return URI::getURIFromRouteName($route);
    }
}
