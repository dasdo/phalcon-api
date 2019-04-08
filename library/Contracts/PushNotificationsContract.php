<?php

namespace Gewaer\Contracts;

use Namshi\Notificator\NotificationInterface;

interface PushNotificationsContract extends NotificationInterface
{
    /**
     * Assemble Notification
     */
    public function assemble();
}
