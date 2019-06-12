<?php

use Baka\Router\RouteGroup;
use Baka\Router\Route;

$routes = [
    Route::get('/')->controller('IndexController'),
    Route::get('/status')->controller('IndexController')->action('status'),
];

$routeGroup = RouteGroup::from($routes)
                ->defaultNamespace('Gewaer\Api\Controllers')
                ->defaultPrefix('/v1');

return $routeGroup->toCollections();
