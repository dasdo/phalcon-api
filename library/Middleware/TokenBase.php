<?php

declare(strict_types=1);

namespace Gewaer\Middleware;

use Gewaer\Http\Request;
use Gewaer\Traits\TokenTrait;
use Phalcon\Mvc\Micro\MiddlewareInterface;
use Gewaer\Exception\UnauthorizedHttpException;

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
    protected function isValidCheck(Request $request): bool
    {
        if (!$request->ignoreJwt() && $request->isEmptyBearerToken()) {
            throw new UnauthorizedHttpException('Missing Token');
        }

        return !$request->ignoreJwt();
    }
}
