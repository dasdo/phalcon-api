<?php

declare(strict_types=1);

namespace Gewaer\Http;

use Phalcon\Http\Request as PhRequest;
use Baka\Http\RouterCollection;
use Phalcon\Mvc\Router\Route;

class Request extends PhRequest
{
    /**
    * @return string
    */
    public function getBearerTokenFromHeader(): string
    {
        return str_replace('Bearer ', '', $this->getHeader('Authorization'));
    }

    /**
     * @return bool
     */
    public function isEmptyBearerToken(): bool
    {
        return true === empty($this->getBearerTokenFromHeader());
    }

    /**
     * Did we specify we dont need to validate JWT Token on this section?
     *
     * @return bool
     */
    public function ignoreJwt(Route $route) : bool
    {
        //did we find the router?
        if (is_array(RouterCollection::getJwtIgnoreRoutes()[$route->getHttpMethods()])) {
            return isset(RouterCollection::getJwtIgnoreRoutes()[$route->getHttpMethods()][md5($route->getPattern())]);
        }

        //nop we dont have this route in ignore jwt
        return false;
    }
}
