<?php

namespace Masterei\Sentry\General;

use Spatie\Permission\Models\Role;

class Install
{
    public static function defaultRoles()
    {
        foreach (Config::get('administrative_access') as $role){
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => Config::GUARD
            ]);
        }
    }
}
