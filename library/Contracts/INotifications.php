<?php

namespace Gewaer\Interfaces;

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
