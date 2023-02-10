<?php

namespace Masterei\Sentry;

use Illuminate\Database\Eloquent\Builder;
use Masterei\Sentry\General\Config;
use Masterei\Sentry\General\Guard;
use Masterei\Sentry\Traits\HasRolesProxy;

trait HasSentry
{
    use HasRolesProxy;

    /**
     * Exclude current authenticated user.
     *
     * @param $query
     * @return mixed
     */
    public function scopeExcludeCurrentAuth($query)
    {
        return $query->where('users.id', '!=', !empty(auth()->user()->id) ? auth()->user()->id : null);
    }

    /**
     * Exclude model data for hidden roles.
     *
     * @param $query
     * @return mixed
     */
    public function scopeExcludeWithHiddenRole($query)
    {
        return $query
            ->select('users.*')
            ->where('model_has_roles.model_type', static::class)
            ->whereNotIn('roles.name', Config::get('hidden_roles'))
            ->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->leftJoin('roles', 'roles.id', '=', 'model_has_roles.role_id');
    }

    /**
     * Return user's assigned role.
     *
     * @return string
     */
    public function getRole(){
        $roles = $this->getRoleNames();
        return $roles->isNotEmpty() ? $roles->first() : null;
    }

    /**
     * Assign role and implement one role per user.
     *
     * @param $role
     * @return HasSentry
     */
    public function grantRole($role)
    {
        $this->roles()->detach();
        $this->syncRoles(collect($role)[0]);
        return $this->singleRoleInstanceResult();
    }

    /**
     * Revoke user assigned role.
     *
     * @void
     */
    public function revokeRole(){
        $this->roles()->detach();
        $this->syncRoles([]);
        return $this->singleRoleInstanceResult();
    }

    /**
     * Override result to a single role per model.
     *
     * @return $this
     */
    protected function singleRoleInstanceResult()
    {
        $this->role = $this->roles->isNotEmpty() ? $this->roles->first() : null;
        unset($this->roles);

        if(isset($this->role->pivot)){
            unset($this->role->pivot);
        }

        return $this;
    }

    /**
     * Check model if it has access to a specific route uri.
     *
     * @param $uri
     * @return bool
     */
    public function hasAccess($uri)
    {
        return $this->hasPermissionTo($uri);
    }

}
