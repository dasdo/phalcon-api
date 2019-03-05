<?php

declare(strict_types=1);

namespace Gewaer\Traits;

use Exception;

/**
 * Trait ResponseTrait
 *
 * @package Gewaer\Traits
 *
 * @property Users $user
 * @property Config $config
 * @property Request $request
 * @property Auth $auth
 * @property \Phalcon\Di $di
 *
 */
trait UsersAssociatedTrait
{
    /**
     * create new related User Associated instance dynamicly
     * @param string $model
     * @return void
     * @todo Find a better way to handle namespaces for models
     */
    public function saveUsersAssociatedModels(string $model): void
    {
        $class = '\Gewaer\Models\UsersAssociated' . ucfirst($model);
        $usersAssociatedModel = new $class();
        $usersAssociatedModel->users_id = $this->user->getId();
        $usersAssociatedModel->companies_id = $this->getId();
        $usersAssociatedModel->apps_id = $this->di->getApp()->getId();
        $usersAssociatedModel->identify_id = $this->user->getId();
        $usersAssociatedModel->user_active = 1;
        $usersAssociatedModel->user_role = 'admin';

        if (!$usersAssociatedModel->save()) {
            throw new Exception((string)current($usersAssociatedModel->getMessages()));
        }
    }
}
