<?php

namespace Gewaer\Notifications\PushNotifications;

use Namshi\Notificator\Notification;
use Gewaer\Contracts\PushNotificationsContract;
use Gewaer\Models\Users;
use Gewaer\Models\Notifications;
use Gewaer\Notifications\PushNotifications\PushNotifications as PushNotifications;
use Gewaer\Traits\NotificationsTrait;

class UsersPushNotifications extends PushNotifications implements PushNotificationsContract
{
    /**
     * Notifications Trait
     */
    use NotificationsTrait;

    /**
     * Constructor
     */
    public function __construct(array $user, string $content, string $systemModule)
    {
        $this->user = $user;
        $this->content  = $content;
        $this->systemModule = $systemModule;
    }

    /**
     * Assemble an Apps Push Notification
     * @todo Create specific assembler for apps push notifications
     */
    public function assemble()
    {
        /**
         * Create a new database record
         */
        self::create($this->user, $this->content, Notifications::USERS, $this->systemModule);

        return $this->content . " From Users";
    }
}
