<?php

namespace Gewaer\Notifications\PushNotifications;

use Namshi\Notificator\Notification;
use Gewaer\Contracts\PushNotificationsContract;
use Gewaer\Models\Users;
use Gewaer\Notifications\PushNotifications\AppsPushNotifications;
use Gewaer\Notifications\PushNotifications\UsersPushNotifications;
use Gewaer\Notifications\PushNotifications\SystemPushNotifications;
use Phalcon\Di;
use PhpAmqpLib\Message\AMQPMessage;

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
     * @param string $content
     * @param string $systemModule
     * @param Users $user
     * @return void
     */
    public static function apps(string $content, string $systemModule, Users $user = null): void
    {
        if (!isset($user)) {
            $user =  Di::getDefault()->getUserData();
        }
        /**
         * Create a new Apps Push Notification
         */
        $notification =  new AppsPushNotifications($user, $content, $systemModule);


        /**
         * Convert notification to Rabbitmq message
         */
        $msg =  new AMQPMessage($notification->assemble(), ['delivery_mode' => 2]);

        $channel = Di::getDefault()->getQueue()->channel();

        $channel->basic_publish($msg, '', 'notifications');
        die();
        

        /**
         * Send to notifications queue
         */
        Di::getDefault()->getManager()->trigger($notification);
    }

    /**
     * Create a new Users Notification
     * @param string $content
     * @param string $systemModule
     * @param Users $user
     * @return void
     */
    public static function users(string $content, string $systemModule, Users $user = null): void
    {
        if (!isset($user)) {
            $user =  Di::getDefault()->getUserData();
        }

        /**
         * Create a new Apps Push Notification
         */
        $notification =  new UsersPushNotifications(Di::getDefault()->getUserData(), $content, $systemModule);

        /**
         * Send to notifications queue
         */
        Di::getDefault()->getManager()->trigger($notification);
    }

    /**
     * Create a new System Notification
     * @param string $content
     * @param string $systemModule
     * @param Users $user
     * @return void
     */
    public static function system(string $content, string $systemModule, Users $user = null): void
    {
        if (!isset($user)) {
            $user =  Di::getDefault()->getUserData();
        }

        /**
         * Create a new Apps Push Notification
         */
        $notification =  new SystemPushNotifications(Di::getDefault()->getUserData(), $content, $systemModule);

        /**
         * Send to notifications queue
         */
        Di::getDefault()->getManager()->trigger($notification);
    }
}
