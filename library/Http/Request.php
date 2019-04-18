<?php

declare(strict_types=1);

namespace Gewaer\Http;

use Phalcon\Http\Request as PhRequest;

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
    public function ignoreJwt() : bool
    {
        return ('//v1/auth' === $this->getURI() || '/v1/auth' === $this->getURI() || '/v1/users' === $this->getURI());
    }
}
