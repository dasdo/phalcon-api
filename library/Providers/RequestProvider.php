<?php

declare(strict_types=1);

namespace Gewaer\Providers;

use Gewaer\Http\Request;
use Gewaer\Http\SwooleRequest;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;

class RequestProvider implements ServiceProviderInterface
{
    /**
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        if (defined('ENGINE') && ENGINE === 'SWOOLE') {
            $container->setShared('request', new SwooleRequest());
        } else {
            $container->setShared('request', new Request());
        }
    }
}
