<?php

namespace Masterei\Sentry;

use Masterei\Sentry\Traits\HasRolesProxy;

trait HasSentry
{
    use HasRolesProxy;

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
}
