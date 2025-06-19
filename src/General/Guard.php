<?php

namespace Masterei\Sentry\General;

use Illuminate\Support\Facades\Auth;
use Masterei\Sentry\Sentry;

class Guard
{
    protected string $currentGuard;

    public function __construct()
    {
        $this->currentGuard = $this->getCurrentGuardWithAuthMiddlewareCheck();

        Cache::ensurePackageCache($this->currentGuard);
    }

    public function accessEvaluator($route, $method)
    {
        // allow all access on debugging mode
        if(Config::get('debug')){
            return true;
        }

        $uri = $this->prepareURI($route, $method);

        // allow if uri is in guest exception list
        if(Sentry::isGuestURI($uri)) {
            return true;
        }

        // allow if uri does not exist in database
        if(!Sentry::verifyURIDatabaseExist($uri, $this->currentGuard)) {
            return true;
        }

        // check authenticated user if it has url access
        if(auth($this->currentGuard)->check() && auth()->user()->hasAccess($uri, $this->currentGuard)) {
            return true;
        }

        return false;
    }

    /**
     * Determine the guard that authenticated the current request,
     * and check if its corresponding `auth:{guard}` middleware was used.
     */
    public function getCurrentGuardWithAuthMiddlewareCheck(): ?string
    {
        $routeMiddlewares = request()->route()?->gatherMiddleware() ?? [];

        foreach (array_keys(config('auth.guards')) as $guard) {
            if (Auth::guard($guard)->check() && in_array("auth:$guard", $routeMiddlewares)) {
                return $guard;
            }
        }

        return null;
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
