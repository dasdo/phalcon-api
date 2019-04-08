<?php

namespace Gewaer\Providers;

use Phalcon\Di\ServiceProviderInterface;
use Phalcon\DiInterface;
use Namshi\Notificator\Manager;
use function Gewaer\Core\appPath;

class EventManagerProvider implements ServiceProviderInterface
{
    /**
     * @param DiInterface $container
     */
    public function register(DiInterface $container)
    {
        $config = $container->getShared('config');

        $container->setShared(
            'manager',
            function () use ($config) {
                $manager = new Manager();

                $handlers =  $this->providers = require appPath('api/config/handlers.php');

                //Pass Handlers as array
                $manager->setHandlers($handlers);

                return $manager;
            }
        );
    }
}
