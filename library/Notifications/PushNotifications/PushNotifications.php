<?php

namespace Gewaer\Notifications\PushNotifications;

use Namshi\Notificator\Notification;
use Gewaer\Contracts\PushNotificationsContract;
use Gewaer\Models\Users;

class PushNotifications extends Notification implements PushNotificationsContract
{
    public $user;

    public $content;

    public $notificationTypeId;

    public $systemModule;

    public function __construct( Users $user,string $content, int $notificationTypeId = 0, string $systemModule)
    {
        $this->user = $user;
        $this->content  = $content;
        $this->notificationTypeId = $notificationTypeId;
        $this->systemModule = $systemModule;
    }

    /**
     * Assemble Notification
     */
    public function assemble()
    {
        return $this->content;
    }
}
