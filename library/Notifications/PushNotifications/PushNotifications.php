<?php

namespace Gewaer\Notifications\PushNotifications;

use Namshi\Notificator\Notification;
use Gewaer\Contracts\PushNotificationsContract;
use Gewaer\Models\Users;
use Gewaer\Models\Notifications;
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
    public static function apps(string $content, string $systemModule, array $user = null): void
    {
        if (!isset($user)) {
            $user =  Di::getDefault()->getUserData();
        }
        /**
         * Create an array of  Apps Push Notification
         */
        $notificationArray =  array(
            'user'=> $user->toArray(),
            'content'=> $content,
            'system_module'=>$systemModule,
            'notification_type_id'=> Notifications::APPS
        );


        /**
         * Convert notification to Rabbitmq message
         */
        $msg =  new AMQPMessage(json_encode($notificationArray, JSON_UNESCAPED_SLASHES), ['delivery_mode' => 2]);

        $channel = Di::getDefault()->getQueue()->channel();

        $channel->basic_publish($msg, '', 'notifications');
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
         * Create an array of  Apps Push Notification
         */
        $notificationArray =  array(
            'user'=> $user->toArray(),
            'content'=> $content,
            'system_module'=>$systemModule,
            'notification_type_id'=> Notifications::USERS
        );


        /**
         * Convert notification to Rabbitmq message
         */
        $msg =  new AMQPMessage(json_encode($notificationArray, JSON_UNESCAPED_SLASHES), ['delivery_mode' => 2]);

        $channel = Di::getDefault()->getQueue()->channel();

        $channel->basic_publish($msg, '', 'notifications');
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
         * Create an array of  Apps Push Notification
         */
        $notificationArray =  array(
            'user'=> $user->toArray(),
            'content'=> $content,
            'system_module'=>$systemModule,
            'notification_type_id'=> Notifications::SYSTEM
        );


        /**
         * Convert notification to Rabbitmq message
         */
        $msg =  new AMQPMessage(json_encode($notificationArray, JSON_UNESCAPED_SLASHES), ['delivery_mode' => 2]);

        $channel = Di::getDefault()->getQueue()->channel();

        $channel->basic_publish($msg, '', 'notifications');
    }
}
