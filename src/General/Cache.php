<?php

namespace Masterei\Sentry\General;

use Illuminate\Support\Facades\DB;
use Masterei\Sentry\Models\Permission;
use Masterei\Sentry\Sentry;
use Spatie\Permission\PermissionRegistrar;

class Cache
{
    public static function flushURIPermissions()
    {
        $sentry = Sentry::getOrig();
        $routes = Assessor::getRoutes();

        return [
            'created' => self::addNewRoutes($sentry, $routes),
            'deleted' => self::removeInactiveRoutes($sentry, $routes)
        ];
    }

    public static function flushDependencyPackageCache()
    {
        return app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public static function flushPackageCache()
    {
        cache()->forget(Config::get('cache.key'));
        cache()->put(Config::get('cache.key'), Sentry::getOrig(), Config::get('cache.expiration_time'));
    }

    protected static function addNewRoutes($sentry, $routes)
    {
        $un_existed_uri = array_diff($routes->pluck('uri')->toArray(), $sentry->pluck('uri')->toArray());

        // populate entries into database
        foreach ($routes as $route){
            Permission::createURIPermission([
                'uri' => $route->uri,
                'method' => $route->method,
                'group' => $route->group,
                'route_name' => $route->route_name,
            ]);
        }

        return Sentry::whereIn(Permission::tableName() . '.name', $un_existed_uri)
            ->get()
            ->map(function ($value){
                return [
                    $value->uri,
                    $value->method,
                    $value->guard_name,
                    $value->pkg_group,
                    $value->pkg_route_name
                ];
            })->toArray();
    }

    protected static function removeInactiveRoutes($sentry, $routes)
    {
        $inactive_uri = array_diff($sentry->pluck('uri')->toArray(), $routes->pluck('uri')->toArray());
        $inactive_data = Sentry::whereIn(Permission::tableName() . '.name', $inactive_uri)
            ->get()
            ->map(function ($value){
                return [
                    $value->uri,
                    $value->method,
                    $value->guard_name,
                    $value->pkg_group,
                    $value->pkg_route_name
                ];
            })->toArray();

        // actual table data deletion
        foreach ($inactive_uri as $uri){
            Permission::findByURI($uri)->delete();
        }

        return $inactive_data;
    }

    public static function ensurePackageCache()
    {
        if(!cache()->has(Config::get('cache.key'))){
            self::flushPackageCache();
        }
    }

    public static function ensurePackageDataConsistency()
    {
        $sentry = Config::get('table_name');
        $permission = Permission::tableName();
        DB::statement("DELETE A FROM $sentry A LEFT JOIN $permission B ON B.id = A.permission_id WHERE B.id IS NULL");
    }

    public static function grantAllURIAccessToAdministrativeRoles()
    {
        foreach (Config::get('administrative_access') as $role){
            Sentry::getRole($role)->grantAllURIAccess();
        }
    }
}
