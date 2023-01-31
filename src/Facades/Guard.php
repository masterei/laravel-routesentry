<?php

namespace Masterei\Sentry\Facades;

use Illuminate\Support\Facades\Facade;
use Masterei\Sentry\General\Guard as BaseGuard;

class Guard extends Facade
{
    protected static function getFacadeAccessor()
    {
        self::clearResolvedInstance(BaseGuard::class);
        return BaseGuard::class;
    }
}
