<?php

namespace Masterei\Sentry\Models;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Traits\HasPermissions;

class Role extends \Spatie\Permission\Models\Role
{
    use HasPermissions {
//        HasPermissions::checkPermissionTo as protected;
//        HasPermissions::convertPipeToArray as protected;
//        HasPermissions::convertToPermissionModels as protected;
//        HasPermissions::ensureModelSharesGuard as protected;
//        HasPermissions::forgetCachedPermissions as protected;
//        HasPermissions::getAllPermissions as protected;
//        HasPermissions::getDefaultGuardName as protected;
//        HasPermissions::getDirectPermissions as protected;
//        HasPermissions::getGuardNames as protected;
//        HasPermissions::getPermissionClass as protected;
//        HasPermissions::getPermissionNames as protected;
//        HasPermissions::getPermissionsViaRoles as protected;
//        HasPermissions::getRoleClass as protected;
//        HasPermissions::getRoleNames as protected;
//        HasPermissions::getStoredPermission as protected;
//        HasPermissions::getStoredRole as protected;
//        HasPermissions::givePermissionTo as protected;
//        HasPermissions::hasAllDirectPermissions as protected;
//        HasPermissions::hasAllPermissions as protected;
//        HasPermissions::hasAllRoles as protected;
//        HasPermissions::hasAnyDirectPermission as protected;
//        HasPermissions::hasAnyPermission as protected;
//        HasPermissions::hasAnyRole as protected;
//        HasPermissions::hasDirectPermission as protected;
//        HasPermissions::hasExactRoles as protected;
//        HasPermissions::hasPermissionTo as protected;
//        HasPermissions::hasPermissionViaRole as protected;
//        HasPermissions::hasRole as protected;
//        HasPermissions::hasWildcardPermission as protected;
//        HasPermissions::permissions as protected;
//        HasPermissions::revokePermissionTo as protected;
//        HasPermissions::scopePermission as protected;
//        HasPermissions::scopeRole as protected;
//        HasPermissions::syncPermissions as protected;
//        HasPermissions::syncRoles as protected;
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('published', function (Builder $builder) {
            $builder->whereNotIn('name', config('sentry.hidden_roles'));
        });
    }

    public function getURIAccess()
    {
        return $this->getAllPermissions();
    }

    public function grantURIAccess(...$uri)
    {
        return $this->syncPermissions(collect($uri)->flatten());
    }

    public function grantAllURIAccess()
    {
        return $this->syncPermissions(\Spatie\Permission\Models\Permission::get()->pluck('name')->toArray());
    }

    public function revokeURIAccess(...$uri)
    {
        return $this->revokePermissionTo(collect($uri)->flatten());
    }
}
