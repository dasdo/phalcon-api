<?php

declare(strict_types=1);

namespace Gewaer\Middleware;

use Gewaer\Http\Request;
use Gewaer\Traits\TokenTrait;
use Phalcon\Mvc\Micro\MiddlewareInterface;
use Gewaer\Exception\UnauthorizedHttpException;
use Phalcon\Mvc\Micro;
use Gewaer\Contracts\RequestJwtInterface;

/**
 * Class AuthenticationMiddleware.
 *
 * @package Niden\Middleware
 */
abstract class TokenBase implements MiddlewareInterface
{
    use TokenTrait;

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function isValidCheck(RequestJwtInterface $request, Micro $app): bool
    {
        $ignoreJwt = $request->ignoreJwt($app['router']->getMatchedRoute());
        if (!$ignoreJwt && $request->isEmptyBearerToken()) {
            throw new UnauthorizedHttpException('Missing Token');
        }

        return !$ignoreJwt;
    }
}
