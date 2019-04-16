<?php

namespace Gewaer\Handlers;

use Namshi\Notificator\Notification\Handler\HandlerInterface;
use Gewaer\Contracts\PushNotifications as PushNotificationsContract;
use Phalcon\Http\Response;
use Namshi\Notificator\NotificationInterface;
use Phalcon\Di;
use Gewaer\Notifications\Mobile\Apps;
use Gewaer\Notifications\Mobile\Mobile;
use Gewaer\Models\Notifications;
use Gewaer\Models\SystemModules;

class PushNotificationsHandler implements HandlerInterface
{
    /**
     * Stablishes type of handler
     */
    public function shouldHandle(NotificationInterface $notification)
    {
        return $notification instanceof PushNotificationsContract;
    }
    
    /**
     * Handles actions to take depending of notifications
     * @param NotificationInterface $notification
     */
    public function handle(NotificationInterface $notification)
    {

        //Push the notification.In this case we are just logging the info
        Di::getDefault()->getLog()->info($notification->assemble());
    }
}
