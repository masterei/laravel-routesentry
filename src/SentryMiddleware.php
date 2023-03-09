<?php

namespace Masterei\Sentry;

use Closure;
use Illuminate\Http\Request;

class SentryMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if(!Sentry::checkAccess()){
            Sentry::throwException();
        }

        return $next($request);
    }
}
