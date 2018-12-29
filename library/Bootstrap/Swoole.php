<?php

declare(strict_types=1);

namespace Gewaer\Bootstrap;

use function Gewaer\Core\appPath;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Micro;
use Dmkit\Phalcon\Auth\Middleware\Micro as AuthMicro;
use Baka\Http\RouterCollection;

/**
 * Class Api
 *
 * @package Gewaer\Bootstrap
 *
 * @property Micro $application
 */
class Swoole extends AbstractBootstrap
{
    /**
     * Run the application
     *
     * @return mixed
     */
    public function run()
    {
        $config = $this->container->getConfig()->jwt->toArray();

        //if the router has jwt ignore url we always overwrite the app config
        $routerJwtIgnoreUrl = RouterCollection::getJwtIgnoreRoutes();
        if (!empty($routerJwtIgnoreUrl)) {
            $config['ignoreUri'] = $routerJwtIgnoreUrl;
        } elseif (!$this->container->getConfig()->application->jwtSecurity) {
            //ignore token validation if disable
            $config['ignoreUri'] = ['regex: *'];
        }
        //JWT Validation
        new AuthMicro($this->application, $config);

        return $this->application->handle($this->container->getRequest()->getServer('request_uri', null, '/'));
    }

    /**
     * @return mixed
     */
    public function setup()
    {
        //set the default DI
        $this->container = new FactoryDefault();
        //set all the services
        $this->providers = require appPath('api/config/providers.php');

        //run my parents setup
        parent::setup();
    }
}
