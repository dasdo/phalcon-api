<?php

namespace Gewaer\Contracts;

use Phalcon\Http\RequestInterface;
use Phalcon\Mvc\Router\Route;

interface RequestJwtInterface extends RequestInterface
{
    /**
     * Did we specify we dont need to validate JWT Token on this section?
     *
     * @return bool
     */
    public function ignoreJwt(Route $route) : bool;

    /**
     * Get the authorization header for jwt
     *
     * @return string
     */
    public function getBearerTokenFromHeader(): string;

    /**
     * Is empty?
     *
     * @return boolean
     */
    public function isEmptyBearerToken(): bool;
}
