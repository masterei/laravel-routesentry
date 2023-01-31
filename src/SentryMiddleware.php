<?php

namespace Masterei\Sentry;

use Closure;
use Illuminate\Http\Request;
use Masterei\Sentry\Facades\Guard;
use Masterei\Sentry\General\URI;
use Spatie\Permission\Exceptions\UnauthorizedException;

class SentryMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if(!Guard::checkAccess()){
            throw UnauthorizedException::forRolesOrPermissions([URI::getCurrentURI()]);
        }

        return $next($request);
    }
}
