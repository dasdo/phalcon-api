<?php

namespace Gewaer\Notifications\PushNotifications;

use Namshi\Notificator\Notification;
use Gewaer\Contracts\PushNotifications as PushNotificationsContract;
use Gewaer\Models\Users;
use Gewaer\Models\Notifications;
use Gewaer\Notifications\PushNotifications\PushNotifications as PushNotifications;
use Gewaer\Traits\NotificationsTrait;

class AppsPushNotifications extends PushNotifications implements PushNotificationsContract
{
    /**
     * Notifications Trait
     */
    use NotificationsTrait;

    /**
     * Assemble an Apps Push Notification
     * @todo Create specific assembler for apps push notifications
     */
    public function assemble()
    {
        /**
         * Create a new database record
         */
        self::create($this->user, $this->content, Notifications::APPS, $this->systemModule);

        return $this->content . " From Apps";
    }
}
