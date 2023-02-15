<?php

namespace Masterei\Sentry\Models\Old;

use Illuminate\Database\Eloquent\Model;
use Masterei\Sentry\Sentry;
use Spatie\Permission\Models\Role as BaseRole;

class Role extends Model
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = (new BaseRole())->getTable();
    }

    protected function baseRoleInstance()
    {
        return BaseRole::hydrate([$this->toArray()])->first();
    }

    public function getURIAccess()
    {
        return $this->baseRoleInstance()->getAllPermissions();
    }

    public function grantURIAccess(...$uri)
    {
        return $this->baseRoleInstance()->syncPermissions(collect($uri)->flatten());
    }

    public function grantAllURIAccess()
    {
        return $this->baseRoleInstance()->syncPermissions(Sentry::getOrigURIList());
    }

    public function revokeURIAccess(...$uri)
    {
        $role = $this->baseRoleInstance();

        $current = $role->getAllPermissions()->pluck('name')->toArray();
        $role->permissions()->detach();

        return $role->syncPermissions(array($current, collect($uri)->flatten()));
    }

    public static function findById($id)
    {
        return parent::find($id);
    }

    public static function findByName($name)
    {
        return parent::where('name', $name)->first();
    }
}
