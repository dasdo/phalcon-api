<?php

declare(strict_types=1);

namespace Gewaer\Middleware;

use Gewaer\Http\Request;
use Gewaer\Traits\ResponseTrait;
use Gewaer\Traits\TokenTrait;
use Phalcon\Mvc\Micro\MiddlewareInterface;
use Gewaer\Exception\NotFoundHttpException;

/**
 * Class AuthenticationMiddleware.
 *
 * @package Niden\Middleware
 */
abstract class TokenBase implements MiddlewareInterface
{
    use ResponseTrait;
    use TokenTrait;

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function isValidCheck(Request $request): bool
    {
        if (!$request->ignoreJwt() && $request->isEmptyBearerToken()) {
            throw new NotFoundHttpException('Missing Token');
        }

        return (
            true !== $request->ignoreJwt()
        );
    }
}
