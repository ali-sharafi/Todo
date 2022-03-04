<?php

namespace Todo;

use Illuminate\Support\Facades\Route;

class Todo
{
    private static $defaultOptions = [
        'namespace' => '\Todo\Http\Controllers',
        'prefix' => 'api'
    ];

    public static function api(array $options = [], $callback = null)
    {
        $callback = $callback ?: function ($router) {
            $router->all();
        };

        $options = array_merge(self::$defaultOptions, $options);

        Route::group($options, function ($router) use ($callback) {
            $callback(new ApiRouteRegistrar($router));
        });
    }
}
