<?php

namespace Gewaer\Contracts;

use Namshi\Notificator\NotificationInterface;

interface PushNotifications extends NotificationInterface
{
    /**
     * Assemble Notification
     */
    public function assemble();
}
