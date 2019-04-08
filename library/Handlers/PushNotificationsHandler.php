<?php

namespace Gewaer\Handlers;

use Namshi\Notificator\Notification\Handler\HandlerInterface;
use Gewaer\Contracts\PushNotificationsContract;
use Phalcon\Http\Response;
use Namshi\Notificator\NotificationInterface;
use Phalcon\Di;
use Gewaer\Notifications\PushNotifications\AppsPushNotifications;
use Gewaer\Notifications\PushNotifications\PushNotifications;
use Gewaer\Models\Notifications;
use Gewaer\Traits\NotificationsTrait;
use Gewaer\Models\SystemModules;

class PushNotificationsHandler implements HandlerInterface
{
    /**
     * Notifications Trait
     */
    use NotificationsTrait;

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
        $this->create($notification->user,$notification->content,$notification->notificationTypeId,$notification->systemModule);
        die();
        $notificationType = $notification->notificationTypeId;

        switch ($notificationType) {
            case '1':
                $newTypeRecord =  new AppsPushNotifications($notification->getMessage(), $notification->notificationTypeId);
                Di::getDefault()->getLog()->info($newTypeRecord->getMessage());
                break;
            // case '2':
            // $notificationType =  new AppsPushNotifications($notification->getMessage(), $notification->source_id);
            // Di::getDefault()->getLog()->info($notificationType->getMessage());
            // break;

            // case '3':
            // $notificationType =  new AppsPushNotifications($notification->getMessage(), $notification->source_id);
            // Di::getDefault()->getLog()->info($notificationType->getMessage());
            // break;
            
            default:
                $newTypeRecord =  new PushNotifications($notification->getMessage(), $notification->notificationTypeId);
                Di::getDefault()->getLog()->info($newTypeRecord->getMessage());
                break;
        }
    }
}
