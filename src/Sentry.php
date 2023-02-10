<?php

namespace Masterei\Sentry;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Masterei\Sentry\Facades\Guard;
use Masterei\Sentry\General\Assessor;
use Masterei\Sentry\General\Config;
use Masterei\Sentry\General\URI;
use Masterei\Sentry\Models\Permission;
use Masterei\Sentry\Models\Role;
use Spatie\Permission\Exceptions\UnauthorizedException;

class Sentry extends Model
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(Config::get('table_name'));
        $this->fillable = Config::FILLABLE;
        $this->hidden = Config::HIDDEN;
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('published', function (Builder $builder) {
            $builder->select([
                self::baseTable() . '.id',
                self::permissionTable() . '.name as uri',
                self::baseTable() . '.method',
                self::permissionTable() . '.guard_name',
                self::baseTable() . '.pkg_group',
                self::baseTable() . '.pkg_route_name',
                self::baseTable() . '.group',
                self::baseTable() . '.route_name',
                self::baseTable() . '.description',
                self::baseTable() . '.created_at',
                self::baseTable() . '.updated_at',
            ])->rightJoin(self::permissionTable(), self::permissionTable() . '.id', '=', self::baseTable() . '.permission_id');
        });
    }

    /** Assign default pkg_group for empty attribute. */
    public function getGroupAttribute($value)
    {
        return !empty($value) ? $value : $this->attributes['pkg_group'];
    }

    /** Assign default pkg_route_name for empty attribute. */
    public function getRouteNameAttribute($value)
    {
        return !empty($value) ? $value : $this->attributes['pkg_route_name'];
    }

    /** Remove '@method' from uri permission. */
    public function getUriAttribute($value)
    {
        $split = explode('@', $value);
        unset($split[count($split) - 1]);

        return implode('', $split);
    }

    protected static function baseTable()
    {
        return Config::get('table_name');
    }

    protected static function permissionTable()
    {
        return Permission::tableName();
    }

    /** Get original results with '@method' included at the end of uri. */
    public static function getOrig(){
        return parent::get()->map(function ($value) {
            return $value->getRawOriginal();
        });
    }

    public static function createRole($entries = [])
    {
        return Role::firstOrCreate([
            'name' => $entries['name'],
            'guard_name' => Config::GUARD
        ]);
    }

    public static function findById($id)
    {
        return parent::where(self::permissionTable() . '.id', $id)->first();
    }

    public static function findByURI($uri)
    {
        return parent::where(self::permissionTable() . '.name', $uri)->first();
    }

    public static function getRole($role)
    {
        return is_numeric($role) ? Role::findById($role) : Role::findByName($role);
    }

    public static function getRoles()
    {
        return Role::get()
            ->reject(function ($value){
                return in_array($value->name, Config::get('hidden_roles'));
            });
    }

    public static function getOrigURIList()
    {
        return self::getOrig()->pluck('uri')->toArray();
    }

    public static function getByGroup($column = 'group')
    {
        return parent::get()
            ->map(function($value){
                switch (strtolower($value->method)){
                    case 'get':
                        $value->_sort = 0;
                        break;
                    case 'post':
                        $value->_sort = 1;
                        break;
                    case 'patch':
                        $value->_sort = 2;
                        break;
                    case 'put':
                        $value->_sort = 3;
                        break;
                    case 'delete':
                        $value->_sort = 4;
                        break;
                }

                return $value;
            })
            ->sortBy(['group', '_sort'])
            ->map(function ($value){
                unset($value->_sort);
                return $value;
            })
            ->groupBy($column);
    }

    public static function checkAccess($route = null, $method = 'get')
    {
        return Guard::accessEvaluator($route, $method);
    }

    public static function isGuestURI($uri = null)
    {
        return Assessor::isGuestURI(empty($uri) ? Route::current()->uri : explode('@', $uri)[0]);
    }

    public static function verifyURIDatabaseExist($uri = null)
    {
        return URI::verifyURIDatabaseExist(!empty($uri) ? $uri : URI::getCurrentURI());
    }

    public static function throwException()
    {
        throw UnauthorizedException::forRolesOrPermissions([URI::getCurrentURI()]);
    }
}
