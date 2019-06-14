<?php

declare(strict_types=1);

namespace Gewaer\Providers;

use function Canvas\Core\appPath;
use Canvas\Providers\RouterProvider as CanvasRouterProvider;

class RouterProvider extends CanvasRouterProvider
{
    /**
     * Returns the array for all the routes on this system.
     *
     * @return array
     */
    protected function getRoutes(): array
    {
        $path = appPath('api/routes');
        $canvsaPath = getenv('CANVAS_CORE_PATH');

        //app routes
        $routes = [
            'api' => $path . '/api.php',
            'canvas' =>  $canvsaPath. '/routes/api.php'
        ];

        return $routes;
    }
}
