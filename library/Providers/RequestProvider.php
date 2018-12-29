<?php

declare(strict_types=1);

namespace Gewaer\Providers;

use Gewaer\Http\Request;
use Gewaer\Http\SwooleRequest;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use function Gewaer\Core\isSwooleServer;

class RequestProvider implements ServiceProviderInterface
{
    /**
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        if (isSwooleServer()) {
            $container->setShared('request', new SwooleRequest());
        } else {
            $container->setShared('request', new Request());
        }
    }
}
