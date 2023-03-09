<?php

namespace Masterei\Sentry\General;

use Illuminate\Support\Facades\Route;

class URI
{
    public static function getCurrentURI()
    {
        return self::formatURI(Route::getCurrentRoute()->uri(), self::getCurrentMethod());
    }

    public static function getCurrentMethod($lowercase = true)
    {
        foreach (Route::getCurrentRoute()->methods() as $cur_method){
            if(in_array($cur_method, Config::ALLOWABLE_METHODS)){
                return $lowercase ? strtolower($cur_method) : $cur_method;
            }
        }

        return $lowercase ? strtolower('GET') : 'GET';
    }

    public static function formatURI($uri, $method = 'get')
    {
        return $uri . '@' . strtolower($method);
    }

    public static function getURIFromRouteName($name)
    {
        foreach (Route::getRoutes()->getRoutes() as $key => $route){
            if(isset($route->action['as']) && $route->action['as'] == $name){
                return self::formatURI($route->uri, array_intersect(Config::ALLOWABLE_METHODS, $route->methods)[0]);
            }
        }

        return null;
    }

    public static function verifyURIDatabaseExist($uri)
    {
        foreach (cache()->get(Config::get('cache.key')) as $value){
            if($value['uri'] == $uri){
                return true;
            }
        }

        return false;
    }
}
