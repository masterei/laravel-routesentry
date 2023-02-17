<?php

namespace Masterei\Sentry\General;

use Illuminate\Support\Facades\Route;

class Assessor
{
    public static function getRoutes()
    {
        return collect(Route::getRoutes()->get())
            ->map(function ($value){
                return (object) [
                    'uri' => $value->uri,
                    'method' => self::filterMethods($value->methods),
                    'route_name' => isset($value->action['as']) ? $value->action['as'] : null,
                    'controller' => isset($value->action['controller']) ? $value->action['controller'] : null,
                    'group' => self::filterController(isset($value->action['controller']) ? $value->action['controller'] : null),
                    'middleware' => isset($value->action['middleware']) ? $value->action['middleware'] : []
                ];
            })->reject(function ($value){
                return !in_array(Config::GUARD, $value->middleware);
            })
            ->reject(function($value){
                return self::isRouteURIException($value->uri);
            })->reject(function($value){
                return self::isControllerException($value->controller);
            })->map(function ($value){
                unset($value->middleware);
                $value->uri = URI::formatURI($value->uri, $value->method);
                return $value;
            })
            ->flatten();
    }

    protected static function filterMethods($methods)
    {
        foreach ($methods as $key => $method){
            if(!in_array(strtoupper($method), Config::ALLOWABLE_METHODS)){
                unset($methods[$key]);
            }
        }

        return array_values($methods)[0];
    }

    protected static function filterController($controller)
    {
        if(empty($controller)){
            return null;
        }

        $strip_namespace = str_ireplace("App\\Http\\Controllers\\", '', explode('@', $controller)[0]);
//        $split = explode('\\', explode('@', $controller)[0]);
        $strip_controller = str_ireplace('controller', '', $strip_namespace);
        $replace_slash = str_replace('\\', ' | ', $strip_controller);

        return preg_replace('/(?<! )(?<!^)(?<![A-Z])[A-Z]_/', ' $0', $replace_slash);
    }

//    protected static function assignMiddleware($route)
//    {
//        return isset($route->action['middleware']) ? $route->action['middleware'] : ['web'];
//    }

    public static function isGuestURI($uri)
    {
        return self::filterURIException($uri, Config::get('guest_uri'));
    }

    protected static function isRouteURIException($uri)
    {
        return self::filterURIException($uri, Config::get('routes_exception.uri'));
    }

    protected static function isControllerException($controller)
    {
        if(isset($controller)){
            foreach (Config::get('routes_exception.controllers') as $key => $exception){
                if(str_contains($controller, $exception)){
                    return true;
                }
            }
        }

        return false;
    }

    protected static function filterURIException($needle, $haystack)
    {
        $regex_combination = [];
        foreach ($haystack as $uri){
            $regex = str_replace('*', '.*', $uri);
            $regex = str_replace('/', '\/', $regex);

            $regex_combination[] = $regex;
        }

        // filter uri combinations
        foreach ($regex_combination as $regex){
            if(preg_match("/^$regex$/i", $needle)){
                return true;
            }
        }

        return false;
    }
}
