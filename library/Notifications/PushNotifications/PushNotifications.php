<?php

namespace Gewaer\Notifications\PushNotifications;

use Namshi\Notificator\Notification;
use Gewaer\Contracts\PushNotificationsContract;
use Gewaer\Models\Users;
use Gewaer\Notifications\PushNotifications\AppsPushNotifications;
use Gewaer\Notifications\PushNotifications\UsersPushNotifications;
use Gewaer\Notifications\PushNotifications\SystemPushNotifications;
use Phalcon\Di;

class PushNotifications extends Notification implements PushNotificationsContract
{

    public $user;

    public $content;

    public $systemModule;

    public function __construct(string $content)
    {
        $this->content  = $content;
    }

    /**
     * Assemble Notification
     */
    public function assemble()
    {
        return $this->content;
    }

    /**
     * Create a new Apps Notification
     * @return void
     */
    public static function apps(string $content, string $systemModule): void
    {
        /**
         * Create a new Apps Push Notification
         */
        $notification =  new AppsPushNotifications(Di::getDefault()->getUserData(),$content,$systemModule);

        /**
         * Send to notifications queue
         */
        Di::getDefault()->getManager()->trigger($notification);
    }

    /**
     * Create a new Users Notification
     * @return void
     */
    public static function users(string $content, string $systemModule): void
    {
        /**
         * Create a new Apps Push Notification
         */
        $notification =  new UsersPushNotifications(Di::getDefault()->getUserData(),$content,$systemModule);

        /**
         * Send to notifications queue
         */
        Di::getDefault()->getManager()->trigger($notification);
    }

    /**
     * Create a new System Notification
     * @return void
     */
    public static function system(string $content, string $systemModule): void
    {
        /**
         * Create a new Apps Push Notification
         */
        $notification =  new SystemPushNotifications(Di::getDefault()->getUserData(),$content,$systemModule);

        /**
         * Send to notifications queue
         */
        Di::getDefault()->getManager()->trigger($notification);
    }
}
