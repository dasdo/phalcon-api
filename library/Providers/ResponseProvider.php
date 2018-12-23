<?php

declare(strict_types=1);

namespace Gewaer\Providers;

use Phalcon\Http\Response;
use Gewaer\Http\SwooleResponse;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;

class ResponseProvider implements ServiceProviderInterface
{
    /**
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        if (defined('ENGINE') && ENGINE === 'SWOOLE') {
            $container->setShared('response', new SwooleResponse());
        } else {
            $container->setShared('response', new Response());
        }
    }
}
