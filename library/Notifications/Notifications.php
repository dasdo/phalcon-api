<?php

namespace Gewaer\Notifications;

use Gewaer\Contracts\INotifications;

class Notifications implements INotifications
{
    /**
     * Create a new Notification
     */
    public function create(Users $user, string $model, string $id, array $msg)
    {
    }

    /**
     * Send Notification
     */
    public function send()
    {
    }
}
