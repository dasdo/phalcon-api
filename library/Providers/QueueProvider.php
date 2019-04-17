<?php

namespace Gewaer\Providers;

use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use function Gewaer\Core\envValue;

class QueueProvider implements ServiceProviderInterface
{
    /**
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        $container->setShared(
            'queue',
            function () {
                //Connect to the queue
                $queue = new \PhpAmqpLib\Connection\AMQPStreamConnection(
                    envValue('RABBITMQ_HOST'),
                    envValue('RABBITMQ_PORT'),
                    envValue('RABBITMQ_DEFAULT_USER'),
                    envValue('RABBITMQ_DEFAULT_PASS')
                );

                return $queue;
            }
        );
    }
}
