<?php

declare(strict_types=1);

namespace Gewaer\Middleware;

use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;
use Gewaer\Exception\ServerErrorHttpException;
use Gewaer\Exception\PermissionException;
use Gewaer\Models\CompaniesSettings;
use Phalcon\Di;

/**
 * Class AclMiddleware
 *
 * @package Gewaer\Middleware
 */
class AclMiddleware implements MiddlewareInterface
{
    /**
     * Call me
     *
     * @param Micro $api
     * @todo need to check section for auth here
     * @return bool
     */
    public function call(Micro $api)
    {
        $auth = $api->getService('auth');
        $router = $api->getService('router');
        $request = $api->getService('request');

        if (!$auth->isIgnoreUri()) {
            // explode() by / , postiion #1 is always the controller , so its the resource ^.^
            $matchRouter = explode('/', $router->getMatchedRoute()->getCompiledPattern());
            $resource = ucfirst($matchRouter[2]); //2 is alwasy the controller of the router
            $userData = $api->getService('userData');

            $action = null;
            // GET -> read
            // PUT -> update
            // DELETE -> delete
            // POST -> create

            //Search current company's app setting with key paid to verify payment status for current company
            $subscriptionPaid = CompaniesSettings::findFirst([
                'conditions' => "companies_id = ?0 and name = 'paid' and is_deleted = 0",
                'bind' => [Di::getDefault()->getUserData()->default_company]
            ]);

            if (!$subscriptionPaid->value) {
                throw new ServerErrorHttpException('Subscription is not active.Please contact your admin');
            }

            switch (strtolower($request->getMethod())) {
                case 'get':
                    $action = 'list';
                    if (preg_match("/\/([0-9]+)(?=[^\/]*$)/", $request->getURI())) {
                        $action = 'read';
                    }
                    break;
                case 'post':
                    $action = 'create';
                    break;
                case 'delete':
                    $action = 'delete';
                    break;
                case 'put':
                case 'patch':
                    $action = 'update';
                    break;
                default:
                    throw new ServerErrorHttpException('No Permission define for this action');
                break;
            }

            //do you have permision
            if (!$userData->can($resource . '.' . $action)) {
                throw new PermissionException('You dont have permission to run this action ' . $action . ' at ' . $resource);
            }
        }

        return true;
    }
}
