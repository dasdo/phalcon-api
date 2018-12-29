<?php

declare(strict_types=1);

namespace Gewaer\Providers;

use Phalcon\Http\Response;
use Gewaer\Http\SwooleResponse;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use function Gewaer\Core\isSwooleServer;

class ResponseProvider implements ServiceProviderInterface
{
    /**
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        if (isSwooleServer()) {
            $container->setShared('response', new SwooleResponse());
        } else {
            $container->setShared('response', new Response());
        }
    }
}
