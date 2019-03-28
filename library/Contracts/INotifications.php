<?php

namespace Gewaer\Contracts;

use Gewaer\Models\Users;

interface INotifications
{
    /**
     * Create a new Notification
     */
    public function create(Users $user, string $model, string $id, array $msg);

    /**
     * Send Notification
     */
    public function send();
}
