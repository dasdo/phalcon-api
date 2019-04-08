<?php

namespace Gewaer\Notifications\PushNotifications;

use Namshi\Notificator\Notification;
use Gewaer\Contracts\PushNotificationsContract;
use Gewaer\Notifications\PushNotifications\PushNotifications as PushNotifications;

class AppsPushNotifications extends PushNotifications implements PushNotificationsContract
{
    public function __construct( Users $user,string $content, int $system_module_id = 0)
    {
        $this->user = $user;
        $this->content  = $content;
        $this->system_module_id = $system_module_id;
    }

    public function assemble()
    {
        return $this->content . " From Apps";
    }
}
