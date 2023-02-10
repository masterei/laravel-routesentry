<?php

namespace Masterei\Sentry;

use Closure;
use Illuminate\Http\Request;

class SentryMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        return !Sentry::checkAccess() ? Sentry::throwException() : $next($request);
    }
}
