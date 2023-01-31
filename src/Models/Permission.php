<?php

namespace Masterei\Sentry\Models;

use Illuminate\Database\Eloquent\Model;
use Masterei\Sentry\General\Config;
use Masterei\Sentry\Sentry;
use Spatie\Permission\Models\Permission as BasePermission;

class Permission extends Model
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = self::tableName();
    }

    protected static function booted()
    {
        parent::booted();

        static::deleting(function(parent $permission) {
            // remove corresponding data from sentry table
            Sentry::where('permission_id', $permission->id)->delete();
      });
    }

    public static function createURIPermission(array $attributes)
    {
        $attributes = (object) $attributes;

        $permission = BasePermission::firstOrCreate([
            'name' => $attributes->uri,
            'guard_name' => Config::GUARD
        ]);

        // create new
        if($permission->wasRecentlyCreated){
            $sentry = new Sentry();
            $sentry->permission_id = $permission->id;
            $sentry->method = strtoupper($attributes->method);
            $sentry->pkg_group = $attributes->group;
            $sentry->pkg_route_name = $attributes->route_name;
            $sentry->save();
        }

        // update if already exist
        else {
            $sentry = Sentry::findById($permission->id);
            $sentry->pkg_group = $attributes->group;
            $sentry->pkg_route_name = $attributes->route_name;
            $sentry->group = !empty($attributes->group) ? $sentry->getRawOriginal('group') : null;
            $sentry->route_name = !empty($attributes->route_name) ? $sentry->getRawOriginal('route_name') : null;
            $sentry->save();
        }

        return collect(array_merge($sentry->makeHidden([
            'permission_id',
        ])->toArray(), $permission->only([
            'name',
            'guard_name'
        ])));
    }

    public static function tableName()
    {
        return (new BasePermission())->getTable();
    }

    public static function findByURI($uri)
    {
        return parent::where('name', $uri)->first();
    }
}
