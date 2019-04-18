<?php

declare(strict_types=1);

namespace Gewaer\Middleware;

use Gewaer\Exception\ModelException;
use Phalcon\Mvc\Micro;
use Phalcon\Http\Request;
use Lcobucci\JWT\ValidationData;
use Exception;
use function Gewaer\Core\envValue;
use Gewaer\Exception\PermissionException;

/**
 * Class TokenValidationMiddleware.
 *
 * @package Gewaer\Middleware
 */
class TokenValidationMiddleware extends TokenBase
{
    /**
     * @param Micro $api
     *
     * @return bool
     * @throws ModelException
     */
    public function call(Micro $api)
    {
        $cache = $api->getService('cache');
        /** @var Config $config */
        $config = $api->getService('config');
        /** @var Request $request */
        $request = $api->getService('request');
        /** @var Response $response */
        $response = $api->getService('response');

        if ($this->isValidCheck($request)) {
            /**
             * This is where we will validate the token that was sent to us
             * using Bearer Authentication.
             *
             * Find the user attached to this token
             */
            $token = $this->getToken($request->getBearerTokenFromHeader());

            $validationData = new ValidationData();
            $validationData->setIssuer(envValue('TOKEN_AUDIENCE'));
            $validationData->setAudience(envValue('TOKEN_AUDIENCE'));
            $validationData->setId($token->getHeader('jti'));
            $validationData->setCurrentTime(time() + 500);

            if (false === $token->validate($validationData)) {
                throw new PermissionException('Invalid Token');
                //return false;
            }
        }

        return true;
    }
}
