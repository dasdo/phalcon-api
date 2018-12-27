<?php

use Baka\Http\RouterCollection;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for api.
 */

$router = new RouterCollection($application);
$router->setPrefix('/v1');
$router->get('/', [
    'Gewaer\Api\Controllers\IndexController',
    'index',
    'options' => [
        'jwt' => false,
    ]
]);

$router->get('/status', [
    'Gewaer\Api\Controllers\IndexController',
    'status',
    'options' => [
        'jwt' => false,
    ]
]);

$router->get('/timezones', [
    'Gewaer\Api\Controllers\TimeZonesController',
    'index',
]);

/**
 * Authentification Calls
 * @var [type]
 */
$router->post('/auth', [
    'Gewaer\Api\Controllers\AuthController',
    'login',
    'options' => [
        'jwt' => false,
    ]
]);

//asociate mobile devices
$router->post('/users/{id}/devices', [
    'Gewaer\Api\Controllers\UsersController',
    'devices',
]);

/**
 * Need to understand if using this can be a performance disadvantage in the future
 */
$defaultCrudRoutes = [
    'users',
    'companies',
    'CompaniesBranches' => 'companies-branches',
    'languages',
    'AppsPlans' => 'apps-plans',
    'RolesAccesList' => 'roles-acceslist',
    'PermissionsResources' => 'permissions-resources',
    'PermissionsResourcesAccess' => 'permissions-resources-access',
    'UsersInvite' => 'users-invite',
    'EmailTemplates' => 'email-templates'
];

foreach ($defaultCrudRoutes as $key => $route) {
    //set the controller name
    $name = is_int($key) ? $route : $key;
    $controllerName = ucfirst($name) . 'Controller';

    $router->get('/' . $route, [
        'Gewaer\Api\Controllers\\' . $controllerName,
        'index',
    ]);

    $router->post('/' . $route, [
        'Gewaer\Api\Controllers\\' . $controllerName,
        'create',
    ]);

    $router->get('/' . $route . '/{id}', [
        'Gewaer\Api\Controllers\\' . $controllerName,
        'getById',
    ]);

    $router->put('/' . $route . '/{id}', [
        'Gewaer\Api\Controllers\\' . $controllerName,
        'edit',
    ]);

    $router->put('/' . $route, [
        'Gewaer\Api\Controllers\\' . $controllerName,
        'multipleUpdates',
    ]);

    $router->delete('/' . $route . '/{id}', [
        'Gewaer\Api\Controllers\\' . $controllerName,
        'delete',
    ]);
}

$router->get('/roles', [
    'Gewaer\Api\Controllers\RolesController',
    'index',
]);

$router->post('/users', [
    'Gewaer\Api\Controllers\AuthController',
    'signup',
    'options' => [
        'jwt' => false,
    ]
]);

$router->put('/auth/logout', [
    'Gewaer\Api\Controllers\AuthController',
    'logout',
]);

$router->post('/auth/forgot', [
    'Gewaer\Api\Controllers\AuthController',
    'recover',
    'options' => [
        'jwt' => false,
    ]
]);

$router->post('/roles-acceslist/{id}/copy', [
    'Gewaer\Api\Controllers\RolesAccesListController',
    'copy',
    'options' => [
        'jwt' => false,
    ]
]);

$router->post('/auth/reset/{key}', [
    'Gewaer\Api\Controllers\AuthController',
    'reset',
    'options' => [
        'jwt' => false,
    ]
]);

$router->post('/users/invite', [
    'Gewaer\Api\Controllers\UsersInviteController',
    'insertInvite',
    'options' => [
        'jwt' => false,
    ]
]);

$router->post('/user-invites/{hash}', [
    'Gewaer\Api\Controllers\UsersInviteController',
    'processUserInvite',
    'options' => [
        'jwt' => false,
    ]
]);

$router->post('/webhook/payments', [
    'Gewaer\Api\Controllers\PaymentsController',
    'handleWebhook',
    'options' => [
        'jwt' => false,
    ]
]);

$router->mount();
